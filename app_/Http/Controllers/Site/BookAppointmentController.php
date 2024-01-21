<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Controller;
use App\Jobs\OnSubmitJob;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\User;
use App\Models\DoctorAvailability;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class BookAppointmentController extends Controller
{
    public $data = [
        'hospitals' => [],
        'departments' => [],
        'doctors' => [],
    ];

    public $ajax;

    protected $rules = [
        'hospital_id' => 'required|numeric',
        'department_id' => 'required|numeric',
        'doctor_id' => 'required|numeric',
        'date' => 'required|date',
        'time' => 'required',
        'payment_type' => 'required',
        // 'patient_type' => 'required',
    ];

    public function __construct()
    {
        $this->middleware('auth:web');
        $this->ajax = new AjaxController();
    }

    /**
     * Show the appointment form
     *
     * @author tanmayap
     * @date 21 dec 2020
     * @return Laravel view
     */
    public function create($doctor = null)
    {
        $this->data['action'] = 'book-an-appointment.store';
        $this->data['hospitals'] = Hospital::latest()->pluck('name_en', 'id');
        $this->data['allowSuggestion'] = false;
        if (!empty($doctor)) {
            $details = Doctor::where('id', $doctor)->with('department', 'hospital')->first();
            $this->data['departments'] = Department::whereId($details->department->id)->pluck('name_en', 'id');
            $this->data['department_id'] = $details->department->id;
            $this->data['doctors'] = Doctor::whereId($doctor)->pluck('name_en', 'id');
            $this->data['doctor_id'] = $doctor;
            $this->data['hospital_id'] = $details->hospital->id;
        }
        $patient = Patient::where('user_id', Auth::user()->id);
        $this->data['patient'] = Patient::where('user_id', Auth::user()->id)->first()->toArray();
        return view('site.e-service.book-appointment', $this->data);
    }

    /**
     * Save the appointment details for the patient
     *
     * @param $request
     *
     * @author tanmayap
     * @date 21 dec 2020
     * @return mixed
     */
    public function store(Request $request)
    {
        $validate = \Validator::make($request->all(), $this->rules);
        if ($validate->fails()) {
            if (!empty($request->doctor_id)) {
                return redirect(route('book-an-appointment', ['doctor' => $request->doctor_id]))
                    ->withErrors($validate->errors())
                    ->withInput();
            }
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            $getMrn = mrnByHospital($request->hospital_id);
            if (empty($getMrn)) {
                session()->flash('message', ['danger' => 'MRN number is not alloted. Please wait till MRN number alloted to you.']);
                return redirect()->back();
            }
            $checkAvailability = DoctorAvailability::where([
                'doctor_id' => $request->doctor_id,
                'shift_name' => $request->shift_name,
                'date' => datetoDB($request->date)
            ])->first();
            // $checkAvailability->schedule_serial;
            $request->request->add([
                'user_id' => Auth::user()->id,
                'schedule_serial' => $checkAvailability->schedule_serial
            ]);
            // Sync Appointment data to the Oracle server
            $this->uploadBooking($request->all());
            $appointment = Appointment::create($request->all());
            $appointmentId = $appointment->id;
            // Refresh the connection
            $recentBookingDetails = Appointment::whereId($appointmentId)->with(
                'hospital', 'department', 'doctor'
            )
                ->first();
            // Assign hospital to patient
            $this->assignHospitalToPatient($request->hospital_id);
            // Send Notification
            $appointmentTime = dbtoDate($recentBookingDetails->date . " " . $recentBookingDetails->time, "m/d/Y h:i A");
            if (env('NOTIFY_MAIL')) {
                $email = [
                    'subject' => 'You have booked an appointment successfully',
                    'greeting' => 'Hi ' . Auth::user()->name,
                    'body' => [
                        "Congrats! You have successfully booked an appointment. Please login to see your appointment details.",
                        "<h3>Your Booking Details are as below: </h3>",
                        "<table><tr><th align='left'>Booking ID</th><td>123</td></tr><tr><th align='left'>MRN</th><td>" . $getMrn . "</td></tr><tr><th align='left'>Patient Name</th><td>" . Auth::user()->name . "</td></tr><tr><th align='left'>Hospital's Name</th><td>" . $recentBookingDetails->hospital->{'name_' . app()->getLocale()} . "</td></tr><tr><th align='left'>Doctor's Name</th><td>" . $recentBookingDetails->doctor->{'name_' . app()->getLocale()} . "</td></tr><tr><th align='left'>Department Name</th><td>" . $recentBookingDetails->department->{'name_' . app()->getLocale()} . "</td></tr><tr><th align='left'>Appointment Time</th><td>" . $appointmentTime . "</td></tr></table>",
                    ],
                    'action_text' => 'Login to Application',
                    'action_url' => url('/user/login'),
                ];
                Notification::send(Auth::user(), new \App\Notifications\UserNotify($email));
            }
            session()->flash('message', ['success' => 'Appointment is received successfully. Please check your mail']);
            return redirect()->back();
        }
        session()->flash('message', ['danger' => 'Something went wrong']);
        return redirect()->back();
    }

    public function uploadBooking($data)
    {
        $shiftId = 0;
        $formatTime = date('H:i:s', strtotime($data['time']));
        $bookingDateTime = date('Y-m-d H:i:s', strtotime($data['date'] . " " . $formatTime));
        $bookingTime = new \DateTime($formatTime);
        $getTimeSlots = \App\Models\DoctorShift::whereHospitalId($data['hospital_id'])->get();
        foreach ($getTimeSlots as $key => $slot) {
            $from = new \DateTime($slot->time_from);
            $to = new \DateTime($slot->time_to);
            if ($bookingTime >= $from && $bookingTime <= $to) {
                $shiftId = $slot->id;
            }
        }
        $getHospital = \App\Models\Hospital::find($data['hospital_id']);
        $getDoctor = \App\Models\Doctor::whereId($data['doctor_id'])->select('ref_id')->first();
        $getDepartment = \App\Models\Department::whereId($data['department_id'])->select('ref_id')->first();
        $bookingInfo = [
            'patient_id' => getPatientId(), //$data['user_id'],
            'hospital_code' => $getHospital->code,
            'shift_id' => $shiftId,
            'speciality_id' => $getDepartment->ref_id,
            'doctor_id' => $getDoctor->ref_id,
            'res_date' => $bookingDateTime,
            'schedule_serial' => $data['schedule_serial'],
            'appointment_status' => 'B'
        ];
        // Update to Main Server Instantly
        $sync = new \App\Http\Controllers\Sync\PatientSync();
        $sync->uploadBookingDetails($bookingInfo);
        
        // Update to Main Server through Jobs and Queues
        // OnSubmitJob::dispatch(['data' => $bookingInfo, 'type' => 'booking_upload'])->delay(now());
    }

    /**
     * Assign a patient to a hospital during booking appointment
     *
     * @param $hospital_id
     *
     * @author tanmaya
     * @date 22 dec 2020
     * @return boolean
     */
    public function assignHospitalToPatient($hospital_id)
    {
        $patient = Patient::whereUserId(Auth::user()->id)->first();
        if (empty($patient->hospital_id)) {
            $patient->hospital_id = $hospital_id;
            $patient->save();
        }
        return true;
    }

    /**
     * Admin can suggest to change any appointments
     *
     * @input $id Appointment ID
     *
     * @date 22 nov 2020
     * @author tanamyap
     */
    public function makeSuggestion($id)
    {
        $appointment = Appointment::find($id);
        $this->data += $appointment->toArray();
        $userId = $appointment->user_id;
        $this->data['action'] = 'appointment.suggestion.save';
        $this->data['allowSuggestion'] = true;
        $this->data['hospitals'] = Hospital::latest()->pluck('name', 'id');
        $this->data['departments'] = getDepartmentsByHospital($this->data['hospital_id']);
        $this->data['doctors'] = getDoctorsByDepartment($this->data['hospital_id'], $this->data['department_id']);
        $this->data['patient'] = Patient::where('user_id', $userId)->first()->toArray();

        $this->data['slots'] = getSlotsByDoctor($this->data['doctor_id'], $this->data['date']);
        return view('site.e-service.book-appointment', $this->data);
    }

    /**
     * Suggestion by Admin for User
     *
     * @date 22 nov 2020
     * @author tanmayap
     */
    public function makeSuggestionPost(Request $request)
    {
        $validate = \Validator::make($request->all(), $this->rules);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            $update = $request->except('_token', 'id');
            $update['is_suggested'] = 1;
            $update['date'] = datetoDB($request->date);
            $update['time'] = timetoDB($request->time);
            $update['suggested_by'] = Auth::user()->id;
            $appointment = Appointment::where('id', $request->id);
            $appointmentDetails = $appointment->with('user')->first();
            $appointment->update($update);
            // dd($update);
            if (env('NOTIFY_MAIL')) {
                // Send Notification
                $email = [
                    'subject' => __('general.appointment_updated'),
                    'greeting' => 'Hi ' . (string) $appointmentDetails->user->name,
                    'body' => [
                        "We found some problem with your appointment so we have made some changes with our appointment. you can see our suggestion below.",
                        "Suggestion: " . $update['suggestion'],
                    ],
                    'action_text' => 'Login to Application',
                    'action_url' => url('/user/login'),
                ];
                $user = User::find($appointmentDetails->user_id);
                Notification::send($user, new \App\Notifications\UserNotify($email));
            }

            session()->flash('message', ['success' => __('general.appointment_updated')]);
            return redirect()->back();
        }
        session()->flash('message', ['danger' => 'Something went Wrong']);
        return redirect()->back();
    }

    /**
     * Cancel a Appointment by user and Admin
     *
     * @date 22 nov 2020
     * @author tanmayap
     */
    public function cancel($id)
    {
        $appointment = Appointment::where('id', $id);
        if ($appointment->count() > 0) {
            $appointment->update(['is_cancelled' => 1]);

            // Send Notification
            if (env('NOTIFY_MAIL')) {
                $email = [
                    'subject' => 'Your appointment is cancelled',
                    'greeting' => 'Hi ' . Auth::user()->name,
                    'body' => [
                        'You have successfully cancelled your appointment. Login to the application to boon an appointment again.',
                    ],
                    'action_text' => 'Login to Application',
                    'action_url' => url('/user/login'),
                ];
                Notification::send(Auth::user(), new \App\Notifications\UserNotify($email));
            }
        }
        return redirect()->back();
    }

    // public function syncfromEarth(Request $request)
    // {
    //     if ($request->isMethod('post')) {
    //         // dd($request->all());
    //         $patientId = $request->pid;
    //         $getPatient = Patient::whereUserId($patientId)->with('mrns')->first();
    //         dd($getPatient->toArray());
    //     }
    // }
}
