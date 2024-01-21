<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\LoginOtp;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Auth;
use Blocktrail\CryptoJSAES\CryptoJSAES;

class AuthController extends Controller
{
    public $jsonResponse, $rules;

    public function __construct()
    {
        $this->jsonResponse = [
            'status' => 0,
            'message' => 'Something went wrong'
        ];
        $this->rules = [
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required',
        ];
    }

    public function sendOtp(Request $request)
    {
        if ($request->isMethod('post')) {
            $rawPayload = json_decode(request()->getContent(), true);
            $decryptPayload = CryptoJSAES::decrypt($rawPayload['data'], env('CRYPTO_SECRET_KEY'));
            $payload = json_decode($decryptPayload, true);

            $user = User::where('email', $payload['email']);
            if ($user->count() > 0) {
                $userDetails = $user->first();
                $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $loginOtp = [
                    'email' => $payload['email'],
                    'otp' => $otp
                ];
                LoginOtp::where('email', $payload['email'])->delete();
                if (LoginOtp::insert($loginOtp)) {
                    $email = [
                        'subject' => "Your login verification code",
                        'greeting' => "Hi $userDetails->fullname,",
                        'body' => [
                            'Here is your login verification code: <br> ' . $otp
                        ],
                        'to' => $userDetails->email,
                        'more' => [],
                        'action_text' => 'Login to Application',
                        'action_url' => url('/user/login'),
                    ];
                    try {
                        \Mail::to($userDetails->email)
                            ->send(new \App\Mail\SendQuoteMail($email));

                        $this->jsonResponse = [
                            'status' => 1,
                            'message' => 'OTP sent to Email address'
                        ];
                    } catch (\Exception $ex) {
                        echo $ex->getMessage();
                    }
                }
            } else {
                $this->jsonResponse = [
                    'status' => 0,
                    'message' => 'You are not yet Registered'
                ];
            }
        }

        return response()
            ->json($this->jsonResponse)
            ->withCallback($request->input('callback'));
    }

    public function optSignin(Request $request)
    {
        if ($request->isMethod('post')) {
            $rawPayload = json_decode(request()->getContent(), true);
            $decryptPayload = CryptoJSAES::decrypt($rawPayload['data'], env('CRYPTO_SECRET_KEY'));
            $payload = json_decode($decryptPayload, true);
            $email = $payload['email'];
            $otp = trim($payload['otp']);
            $checkOtp = LoginOtp::where(['email' => $email, 'otp' => $otp])->count();
            if ($checkOtp >= 1) {
                $user = User::where('email', $email)->first();
                if (Auth::loginUsingId($user->id)) {
                    $token = $user->createToken('auth-token')->plainTextToken;
                    $this->jsonResponse = [
                        'status' => 1,
                        'token' => $token,
                        'user' => \Arr::only($user->toArray(), [
                            'name', 'email', 'mobile', 'postcode', 'role', 'is_active', 'created_at', 'profile_photo_url'
                        ]),
                        'message' => 'Login Successfull'
                    ];
                } else {
                    $this->jsonResponse = [
                        'status' => 0,
                        'message' => 'Username or password is wrong'
                    ];
                }
            }
        }
        return response()
            ->json($this->jsonResponse)
            ->withCallback($request->input('callback'));
    }

    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $rawPayload = json_decode(request()->getContent(), true);
            $decryptPayload = CryptoJSAES::decrypt($rawPayload['data'], env('CRYPTO_SECRET_KEY'));
            $payload = json_decode($decryptPayload, true);
            $validate = Validator::make($payload, [
                'email' => 'required|string|email|max:255',
                'password' => 'required',
            ]);
            if ($validate->fails()) {
                $errors = $validate->errors()->toArray();
                $getErrors = [];
                foreach ($errors as $error) {
                    $getErrors[] = $error[0];
                }
                $this->jsonResponse = [
                    'status' => 0,
                    'message' => 'Validation Error',
                    'errors' => $getErrors
                ];
            } else {
                if (Auth::attempt(['email' => $payload['email'], 'password' => $payload['password']])) {
                    $user = User::whereEmail($payload['email'])->first();
                    $token = $user->createToken('auth-token')->plainTextToken;
                    $this->jsonResponse = [
                        'status' => 1,
                        'token' => $token,
                        'user' => \Arr::only($user->toArray(), [
                            'name', 'email', 'mobile', 'postcode', 'role', 'is_active', 'created_at', 'profile_photo_url'
                        ]),
                        'message' => 'Login Successfull'
                    ];
                } else {
                    $this->jsonResponse = [
                        'status' => 0,
                        'message' => 'Username or password is wrong'
                    ];
                }
            }
        }


        return response()
            ->json($this->jsonResponse)
            ->withCallback($request->input('callback'));
    }

    public function signup(Request $request)
    {
        if ($request->isMethod('post')) {
            $payload = json_decode(request()->getContent(), true);
            $validate = \Validator::make($payload, $this->rules);
            if ($validate->fails()) {
                // dump($validate->errors()->toArray());
                $errors = $validate->errors()->toArray();
                $getErrors = [];
                foreach ($errors as $error) {
                    $getErrors[] = $error[0];
                }
                $this->jsonResponse = [
                    'status' => 0,
                    'message' => 'Validation Error',
                    'errors' => $getErrors
                ];
            } else {
                $signup = [
                    "name" => $payload['name'],
                    "role" => 'user',
                    "email" => $payload['email'],
                    "plain_password" => $payload['password'],
                    "password" => $payload['password'],
                ];
                $user = new User($signup);
                $user->save();
                if ($user->id) {
                    $this->jsonResponse = [
                        'status' => 1,
                        'message' => 'Signup Successfull'
                    ];
                }
            }
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function userDetails(Request $request)
    {
        if ($request->isMethod('post')) {
            $payload = json_decode(request()->getContent(), true);
            if (!empty($payload['email'])) {
                $user = User::whereEmail($payload['email'])
                    ->select(
                        'name',
                        'email',
                        'mobile',
                        'address',
                        'email_verified_at',
                        'postcode',
                        'profile_photo_path',
                        'is_active',
                        'created_at'
                    );
                if ($user->count() > 0) {
                    $this->jsonResponse = [
                        'status' => 1,
                        'data' => $user->first()->toArray()
                    ];
                }
            }
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function updateUserDetails(Request $request)
    {
        if ($request->isMethod('post')) {
            $payload = json_decode(request()->getContent(), true);
            // Check existance of incoming data
            $userId = null;
            $user = User::whereEmail($payload['anchor']);
            if ($user->count() > 0) {
                $userData = $user->first();
                $userId = $userData->id;
            }
            $updateData = json_decode($payload['data'], true);

            $validate = \Validator::make($updateData, [
                'name' => 'required',
                'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            ]);
            if ($validate->fails()) {
                // dump($validate->errors()->toArray());
                $errors = $validate->errors()->toArray();
                $getErrors = [];
                foreach ($errors as $error) {
                    $getErrors[] = $error[0];
                }
                $this->jsonResponse = [
                    'status' => 0,
                    'message' => 'Validation Error',
                    'errors' => $getErrors
                ];
            } else {
                $updateData = \Arr::except($updateData, [
                    'profile_photo_url', 'profile_photo_path', 'created_at', 'email_verified_at', 'is_active'
                ]);
                try {
                    if (User::whereEmail($payload['anchor'])->update($updateData)) {
                        $this->jsonResponse = [
                            'status' => 1,
                            'message' => 'User details updated successfully'
                        ];
                    }
                } catch (\Exception $ex) {
                    $this->jsonResponse = [
                        'status' => 0,
                        'message' => "Some Exception occured during the updation process. Please check and try again"
                    ];
                }
            }
        }

        return response()
            ->json($this->jsonResponse);
    }

    /**
     * Update Old password with a new password
     */
    public function updatePassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $getData = json_decode(request()->getContent(), true);
            $payload = json_decode($getData['data'], true);

            // Check existance of incoming data
            $user = User::whereEmail($payload['email']);
            if ($user->count() > 0) {
                $userData = $user->first();
                $userPassword = $userData->password;
                if (\Hash::check($payload['old_password'], $userPassword)) {
                    User::whereId($userData->id)->update([
                        'password' => \Hash::make($payload['password']),
                    ]);
                    $this->jsonResponse = [
                        'status' => 1,
                        'message' => 'Password updated successfully'
                    ];
                }
            }
        }

        return response()
            ->json($this->jsonResponse);
    }
}
