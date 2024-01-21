<?php

namespace App\Http\Controllers\Site\Auth;

use App\Http\Controllers\Controller;
// use App\Models\City;
// use App\Models\Country;
// use App\Models\Gender;
// use App\Models\Hospital;
// use App\Models\NationalIdType;
// use App\Models\Nationality;
use App\Models\Patient;
// use App\Models\Religion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public $data;
    /**
     * Initializing Form parameters
     *
     * @author tanmayapatra09
     * @date 29 Oct 2020
     */
    public $form = [
        'firstname' => '',
        'secondname' => '',
        'thirdname' => '',
        'lastname' => '',
        'nationality' => '',
        'dob' => '',
        'national_id' => '',
        'religion' => '',
        'email' => '',
        'phone' => '',
        'gender' => '',
    ];

    public $national_id_types;

    public $nationalities, $genders, $religions, $country, $city;
    public $required = '*';
    protected $rules = [
        'firstname' => 'required|min:3',
        'secondname' => 'required|min:3',
        'thirdname' => 'required|min:3',
        'lastname' => 'required|min:3',
        // 'firstname_ar' => 'required',
        // 'secondname_ar' => 'required',
        // 'thirdname_ar' => 'required',
        // 'lastname_ar' => 'required',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|digits_between:10,15',
        'gender' => 'required',
        'national_id' => 'required|numeric',
        'country_id' => 'required',
        'city_id' => 'required',
        'nationality' => 'required',
        'religion' => 'required',
        'dob' => 'required',
        'terms_and_conditions' => 'required',
    ];
    protected $rules2 = [
        'mrn_number' => 'required',
        'national_id_type' => 'required',
        'hospital_code' => 'required',
        'national_id' => 'required',
    ];

    public function __construct()
    {
        // $this->data['required'] = '*';
        // $this->data['nationalities'] = Nationality::pluck('name_' . app()->getLocale(), 'id');
        // $this->data['hospitals'] = Hospital::pluck('name_' . app()->getLocale(), 'code');
        // $this->data['countries'] = Country::where('id', 61)->pluck('name', 'id');
        // $this->data['cities'] = City::pluck('name', 'id');
        // // load genders
        // $this->data['genders'] = Gender::pluck('name', 'code');
        // $this->data['national_id_types'] = NationalIdType::pluck('name', 'id');
        // $this->data['religions'] = Religion::pluck('name', 'id');
    }

    /**
     * Rendering Element to view
     * @author tanmayapatra09
     * @date 29 Oct 2020
     */
    public function show(Request $request)
    {
        $this->data['title'] = __("new_registration");
        $this->data['type'] = $request->type ?? "np";
        return view('site.auth.register-patient', $this->data);
    }

    /**
     * Register a Patient to System
     *
     * @author tanmayapatra09
     * @date 20 Oct 2020
     */

    public function addPatient(Request $request)
    {
        $validate = \Validator::make($request->all(), $this->rules);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            $password = Str::random(10);
            $fullname = Str::of($request->firstname)->append('' . $request->secondname)->append(' ' . $request->thirdname)->append(' ' . $request->lastname);
            $user = User::create([
                'name' => $fullname,
                'role' => 'patient',
                'email' => $request->email,
                'password' => $password,
                'role_id' => 2,
            ]);
            $request->request->add(['user_id' => $user->id, 'created_by' => $user->id]);
            $patient = Patient::create($request->all());
            // Generate a temp MRN number and update to Database
            $tempMRN = 'PAT' . Str::padLeft($patient->id, 10, '0');
            $patient->mrn = $tempMRN;
            $patient->save();
            
            // Initiate Job batch to do the Sync
            $batch = $this->runJobs();
            
            // Send Notification
            $email = [
                'subject' => 'Successfull Registration',
                'greeting' => 'Hi ' . (string) $fullname,
                'body' => [
                    'Congrats! You have successfully Registered into our application. You can login and use our application now onwards.',
                    'Your Login details is as below: ',
                    'Username/Email: ' . $request->email,
                    'Password: ' . $password,
                    '<strong>Make sure not to share your personal login details with anyone!</strong>',
                ],
                'action_text' => 'Login to Application',
                'action_url' => url('/user/login'),
            ];
            Notification::send($user, new \App\Notifications\UserNotify($email));
            // Update Admin for Registration
            $notify['data'] = (string) $fullname . " newly registered into the application recently.";
            $this->addToHistory($notify);

            session()->flash('message', ['success' => 'Congratulations! Your account has been successfully created. Please check you email for more information.']);
            return redirect()->back();
        }
        session()->flash('message', ['danger' => 'Something went Wrong']);
        return redirect()->back();
    }

    public function runJobs()
    {
        // \App\Jobs\OnSubmitJob::dispatch(['type' => 'patient_save'])->delay(now());
        // \App\Jobs\OnSubmitJob::dispatch(['type' => 'patient_update'])->delay(now());
        // \App\Jobs\OnSubmitJob::dispatch(['type' => 'mrn_sync'])->delay(now());

        // $batch = \Illuminate\Support\Facades\Bus::batch([])->dispatch();
        // foreach ($jobs as $key => $job) {
        //     $batch->add(new \App\Jobs\OnSubmitJob([
        //         'type' => $job
        //     ]));
        // }

        // return $batch;
    }

    public function addPatientByMrn(Request $request)
    {
        $validate = \Validator::make($request->all(), $this->rules2);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            $getPatient = new \App\Http\Controllers\Sync\MasterDataSync();
            $details = $getPatient->getPatientDetails($request->hospital_code, $request->mrn_number);
            if (!empty($details[0])) {
                $patientDetails = (array) $details[0];
                if (!empty($patientDetails['ID_TYPE'])
                    && $patientDetails['ID_TYPE'] == $request->national_id_type
                    && !empty($patientDetails['ID_NUMBER'])
                    && $patientDetails['ID_NUMBER'] == $request->national_id
                ) {
                    $checkPatient = Patient::where([
                        'national_id' => $patientDetails['ID_NUMBER'],
                        'national_id_type' => $patientDetails['ID_TYPE'],
                    ])->count();
                    if ($checkPatient == 0) {
                        $savePatient = [
                            'firstname' => $patientDetails['F_NAME_EN'],
                            'secondname' => $patientDetails['S_NAME_EN'],
                            'thirdname' => $patientDetails['T_NAME_EN'],
                            'lastname' => $patientDetails['L_NAME_EN'],
                            'firstname_ar' => $patientDetails['F_NAME'],
                            'secondname_ar' => $patientDetails['S_NAME'],
                            'thirdname_ar' => $patientDetails['T_NAME'],
                            'lastname_ar' => $patientDetails['L_NAME'],
                            'gender' => $patientDetails['GENDERCODE'],
                            'national_id' => $patientDetails['ID_NUMBER'],
                            'national_id_type' => $patientDetails['ID_TYPE'],
                            'religion' => $patientDetails['RELIGION'],
                            'nationality' => $patientDetails['NATIONALITY'],
                            'dob' => date('Y-m-d', strtotime($patientDetails['DATEOFBIRTH'])),
                            'MOBILE' => $patientDetails['MOBILE'],
                            'email' => !empty($patientDetails['EMAIL']) ? $patientDetails['EMAIL'] : $request->national_id . "@gmail.com",
                            'created_at' => date('Y-m-d', strtotime($patientDetails['REGIST_DATE'])),
                        ];
                        $this->confirmOnRegistration($savePatient);
                        session()->flash(
                            'message',
                            ['success' => 'Congratulations! Your account has been successfully created. Please check you email for more information.']
                        );
                        return redirect()->back();
                    } else {
                        session()->flash('message', ['danger' => 'Another user already exists. Please try to login']);
                        return redirect()->back();
                    }
                } else {
                    session()->flash('message', ['danger' => 'Data not found. Please try again']);
                    return redirect()->back();
                }
            }
        }
    }

    public function confirmOnRegistration($rawData)
    {
        $password = Str::random(10);
        $fullname = Str::of($rawData['firstname'])->append('' . $rawData['secondname'])->append(' ' . $rawData['thirdname'])->append(' ' . $rawData['lastname']);
        $user = User::create([
            'name' => $fullname,
            'role' => 'patient',
            'email' => $rawData['email'],
            'password' => $password,
            'role_id' => 2,
        ]);
        $rawData += ['user_id' => $user->id, 'created_by' => $user->id];
        // $request->request->add(['user_id' => $user->id, 'created_by' => $user->id]);
        $patient = Patient::create($rawData);
        // Generate a temp MRN number and update to Database
        $tempMRN = 'PAT' . Str::padLeft($patient->id, 10, '0');
        $patient->mrn = $tempMRN;
        $patient->save();

        // Send Notification
        $email = [
            'subject' => 'Successfull Registration',
            'greeting' => 'Hi ' . (string) $fullname,
            'body' => [
                'Congrats! You have successfully Registered into our application. You can login and use our application now onwards.',
                'Your Login details is as below: ',
                'Username/Email: ' . $rawData['email'],
                'Password: ' . $password,
                '<strong>Make sure not to share your personal login details with anyone!</strong>',
            ],
            'action_text' => 'Login to Application',
            'action_url' => url('/user/login'),
        ];
        Notification::send($user, new \App\Notifications\UserNotify($email));
        // Update Admin for Registration
        $notify['data'] = (string) $fullname . " newly registered into the application recently.";
        $this->addToHistory($notify);
    }

    public function sendEmailVerification()
    {
        $user = User::find(27);
        $verifyUser = [
            'email' => $user->email,
            'token' => sha1(time()),
            'created_at' => date('Y-m-d h:i:s'),
        ];
        \DB::table('password_resets')->insert($verifyUser);

        $email = [
            'subject' => 'Email Confirmation',
            'greeting' => 'Hi ' . (string) $user->full_name,
            'body' => [
                'You have successfully created your account in our application. Please confirm your email id by clicking on below link so that you can take advantages of our facilities',
            ],
            'action_text' => 'Verify email address',
            'action_url' => url('/user/login'),
        ];
        Notification::send($user, new \App\Notifications\UserNotify($email));
    }

    public function emailVerificationCheck(Request $request, $token)
    {
        $email = $request->email;
        $getDetails = \DB::table('password_resets')->where('email', $email)->first();
        if ($getDetails->token == $token) {
            User::whereEmail($email)->update(['email_verified_at' => \Carbon\Carbon::now()]);
            session()->flash('message', ['success' => __('email_verification_success')]);
            return redirect(url('/'));
        } else {
            session()->flash('message', ['danger' => __('email_verification_error')]);
            return redirect(url('/'));
        }
    }

    /**
     * Forgot password form
     */
    public function forgotPassword(Request $request)
    {
        $this->data['title'] = __("forgot_password");
        $this->data['target'] = "forgot.password.send";
        // if (!empty($request->token)) {
        //     $this->data['target'] = "forgot.password.change.save";
        //     $this->data['type'] = "change";
        //     $this->data['email'] = $request->email;
        // }
        return view('site.auth.forgot-password', $this->data);
    }

    /**
     * Send users their forgot password recovery link
     */
    public function forgotPasswordSend(Request $request)
    {
        $check = User::whereEmail($request->email)->first();
        if (!empty($check)) {
            $token = sha1(time());
            $verifyUser = [
                'email' => $check->email,
                'token' => $token,
                'created_at' => date('Y-m-d h:i:s'),
            ];
            \DB::table('password_resets')->insert($verifyUser);
            $email = [
                'subject' => __("forgot_password"),
                'greeting' => 'Hi ' . (string) $check->name,
                'body' => [
                    'You recently requested to reset your password for your ' . env('APP_NAME') . ' account. Click the below button to reset it.',
                ],
                'action_text' => __("reset_your_password"),
                'action_url' => url('/forgot-password/verify?email=' . $check->email . '&token=' . $token),
            ];
            Notification::send($check, new \App\Notifications\UserNotify($email));
            session()->flash('message', ['success' => __('sent_forget_password_link')]);
            return redirect()->back();
        } else {
            session()->flash('message', ['danger' => __('could_not_find_email')]);
            return redirect()->back();
        }
    }
    public function checkToken($email, $token)
    {
        $check = \DB::table('password_resets')->where(['email' => $email, 'token' => $token])->first();
        if (!empty($check)) {
            return true;
        }
        return false;
    }
    /**
     * Verufy users forgot password link
     */
    public function verifyForgotPassword(Request $request)
    {
        if ($request->method() == "POST") {
            if ($request->password == $request->re_enter_password) {
                if ($this->checkToken($request->email, $request->token)) {
                    $newPassword = $request->password;
                    $user = User::whereEmail($request->email)->first();
                    $user->password = $request->password;
                    $user->save();
                    // Destroy token generation
                    \DB::table('password_resets')->where(['email' => $request->email, 'token' => $request->token])->delete();
                    // Notify User
                    Notification::send($user, new \App\Notifications\TaskComplete(["data" => "Your password change was successful.", "type" => "Password Change"]));
                    session()->flash('message', ['success' => __('password_reset_complete')]);
                    return redirect(route('user.login'));
                }
            } else {
                session()->flash('message', ['danger' => __('password_confirm_password_different')]);
                return redirect()->back();
            }
        } else {
            if ($this->checkToken($request->email, $request->token)) {
                $this->data['title'] = __("forgot_password");
                $this->data['target'] = "forgot.password.verify";
                $this->data['type'] = "change";
                $this->data['email'] = $request->email;
                $this->data['token'] = $request->token;
                return view('site.auth.forgot-password', $this->data);
            } else {
                session()->flash('message', ['danger' => __('token_is_expired')]);
                return redirect(route('user.login'));
            }
        }
    }
}
