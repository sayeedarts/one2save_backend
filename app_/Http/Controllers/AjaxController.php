<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\HospitalDepartment;
use App\Models\Subscriber;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public $jsonResponse = [
        'status' => 0,
        'message' => 'Something went wrong',
    ];

    /**
     * Get department list by Hostpital ID
     */
    public function departmentsByHospital(Request $request)
    {
        $departmentPool = [];
        $hospitalId = $request->hospital;
        $departments = HospitalDepartment::where('hospital_id', $hospitalId)->with('department')->get();
        foreach ($departments as $key => $department) {
            $departmentPool += [
                $department['department']['id'] => $department['department']['name_en'],
            ];
        }
        if (!empty($departmentPool)) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => sortArray($departmentPool),
            ];
        }
        return \json_encode($this->jsonResponse);
    }

    public function doctorsByDepartments(Request $request)
    {
        $doctorPool = [];
        $departmentId = $request->department;
        $hospitalId = $request->hospital;
        $doctorList = Doctor::where([
            'hospital_id' => $hospitalId,
            'department_id' => $departmentId,
        ])->latest()->pluck('name_en', 'id');

        if (!empty($doctorList)) {
            foreach ($doctorList as $doctorId => $doctor) {
                if ($this->ifAvailabilityExists($doctorId)) {
                    $doctorPool[$doctorId] = $doctor;
                }
            }
            // dd($doctorPool);
            $this->jsonResponse = [
                'status' => 1,
                'data' => $doctorPool,
            ];
        }
        return \json_encode($this->jsonResponse);
    }

    public function ifAvailabilityExists($doctorId)
    {
        $exists = \App\Models\DoctorAvailability::whereDoctorId($doctorId)->count();
        return !empty($exists) ? true : false;
    }

    public function getAvailabilityDates(Request $request)
    {
        $doctorId = $request->doctor;
        $dates = \App\Models\DoctorAvailability::whereDoctorId($doctorId)
            ->whereDate('date', '>=', now())
            ->orderBy('date')
            ->pluck('date')
            ->toArray();
        $availableDates = array_unique($dates);
        $startDate = dbtoDate(reset($availableDates));
        $endDate = dbtoDate(end($availableDates));
        return \json_encode([
            'status' => 1,
            'start' => $startDate,
            'end' => $endDate
        ]);
    }
    /**
     * Get Slots list by Date and Doctor's ID
     * 
     * 
     */
    public function shiftsByDoctor(Request $request)
    {
        $doctorId = $request->doctor;
        $date = $request->date;
        $shifts = \App\Models\DoctorAvailability::where(
            [
                'doctor_id' => $doctorId,
                'date' => datetoDB($date),
            ]
        )->pluck('duty_type', 'shift_name');
        if($shifts->isNotEmpty()) {
            // dd($shifts);
            $this->jsonResponse = [
                'status' => 1,
                'data' => $shifts,
            ];
        }
        return \json_encode($this->jsonResponse);
    }

    public function slotsByDoctor(Request $request)
    {
        $doctorId = $request->doctor;
        $hospitalId = $request->hospital;
        $departmentId = $request->department;
        $date = $request->date;
        $shift = $request->shift;
        $day = Carbon::parse($date)->format('l');

        $slotDetails = \App\Models\DoctorAvailability::where(
            [
                'doctor_id' => $doctorId,
                'day' => $day,
                'shift_name' => $shift
            ]
        )->first();
        // dd($slots->toArray());
        $slotValues = [];
        if (!empty($slotDetails)) {
            // foreach ($slots as $key => $slot) {
                if (!empty($slotDetails->from) && !empty($slotDetails->to)) {
                    $slotStartTime = $slotDetails->from;
                    $slotEndTime = $slotDetails->to;
                    $getSlots = $this->getSlots(
                        $date,
                        $slotStartTime,
                        $slotEndTime,
                        $hospitalId,
                        $departmentId,
                        $doctorId
                    );

                    if (!empty($getSlots)) {
                        $slotValues = array_merge($slotValues, $getSlots);
                        // dd($getSlots);
                    }
                }
            // }
        }
        if (!empty($slotValues)) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => $slotValues,
            ];
        }
        return \json_encode($this->jsonResponse);
    }

    /**
     * Get available doctor's slots in between given start and end times
     */
    public function getSlots($date, $start, $end, $hospitalId, $departmentId, $doctorId)
    {
        $start = $this->removeMeridiem($start);
        $end = $this->removeMeridiem($end);

        $startTime = date('H:i', strtotime($start));
        $endTime = date('H:i', strtotime('-15 minutes', strtotime($end)));
        $defTime = date('h:i A', strtotime($start));
        // echo strtotime(date('H:i:s')) . " >> " . time(); exit;
        // echo $defTime; exit;
        $timeSlots = [];
        // push strat time to the data pool
        $timeSlots[] = [
            'meridiem' => date('H', strtotime($defTime)) < 18 ? "AM" : "PM",
            'time' => $defTime, 
            'status' => checkSlot($hospitalId, $departmentId, $doctorId, $date, $defTime),
            'is_disabled' => $this->checkDisability($date, $defTime)
        ];
        while (strtotime($startTime) < strtotime($endTime)) {
            $dateTime = new \DateTime($startTime);
            // echo $startTime; exit;
            $isDisabled = $this->checkDisability($date, $startTime); //strtotime($startTime) <= time() ? true : false;
            $startTime = $dateTime->modify('+15 minutes')->format('h:i A');
            $timeSlots[] = [
                'meridiem' => date('H', strtotime($startTime)) < 18 ? "AM" : "PM",
                'time' => date('h:i A', strtotime($startTime)),
                'status' => checkSlot($hospitalId, $departmentId, $doctorId, $date, $startTime),
                'is_disabled' => $isDisabled
            ];
        }

        return $timeSlots;
    }

    public function checkDisability($date, $time)
    {
        // echo $date; exit;
        // echo now(); exit;
        if ((strtotime($date) == strtotime(date('m/d/Y'))) && (strtotime($time) <= time())) {
            return true;
        }

        return false;
    }

    /**
     * Remove AM or PM from the Time string
     */
    public function removeMeridiem($timeString)
    {
        if (!empty($timeString)) {
            $explode = explode(' ', $timeString);
            return $explode[0] ;
        }
        return null;
    }

    public function addNewsLetter(Request $request)
    {
        $validate = \Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribers,email',
        ]);
        if (!$validate->fails()) {
            Subscriber::create($request->all());
        }
        return \json_encode([
            'status' => 1,
            'message' => "Successfully Subscribed !",
        ]);
    }
}
