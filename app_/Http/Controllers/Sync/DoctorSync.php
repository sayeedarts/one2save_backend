<?php

namespace App\Http\Controllers\Sync;

// use App\Http\Controllers\Controller;
use App\Http\Controllers\Sync\MasterDataSync;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\DoctorShift;
use App\Models\Hospital;
use App\Models\HospitalDepartment;
use App\Models\User;
use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\DB;

class DoctorSync extends MasterDataSync
{
    private $shiftList;

    public function __construct()
    {
        $this->shiftList = shiftTimes();
        parent::__construct();
    }

    /**
     * Sync Specialities from HMH Server
     *
     * @author Tanmaya
     * @date 02 Apr 2021
     * @return none
     */
    public function syncDepartments()
    {
        $speciality = $splHospital = [];
        $hospitals = Hospital::pluck('id', 'code');
        foreach ($hospitals as $hospitalCode => $hospitalId) {
            $response = Http::asForm()->post($this->ociApp . $this->specialityApi, [
                'hospital' => $hospitalCode,
            ]);
            if ($response->status() == 200) {
                $splPerHospital = \json_decode($response->body());
                if (!empty($splPerHospital)) {
                    foreach ($splPerHospital as $key => $speclty) {
                        $saveData = [
                            'ref_id' => $speclty->SPECIALTY_ID,
                            'name_en' => $this->casing($speclty->SPECIALTY_NAME_EN),
                            'name_ar' => $this->casing($speclty->SPECIALTY_NAME_AR),
                        ];
                        $checkDepartment = Department::where([
                            'ref_id' => intval($saveData['ref_id']),
                            'name_en' => trim($saveData['name_en']),
                        ]);
                        if ($checkDepartment->count() == 0) {
                            $deptSave = new Department($saveData);
                            $deptSave->save();
                            if ($deptSave->id > 0) {
                                $deptHospitalData = [
                                    'hospital_id' => $hospitalId,
                                    'department_id' => $deptSave->id,
                                ];
                                $deptHospSave = new HospitalDepartment($deptHospitalData);
                                $deptHospSave->save();
                            }
                        } else {
                            $getDepartment = $checkDepartment->first();
                            $deptHospitalData = [
                                'hospital_id' => $hospitalId,
                                'department_id' => $getDepartment->id,
                            ];
                            // Check in relation
                            $ifRelationExist = HospitalDepartment::where($deptHospitalData)->count();
                            if ($ifRelationExist == 0) {
                                $deptHospSave = new HospitalDepartment($deptHospitalData);
                                $deptHospSave->save();
                            }
                        }
                    }
                }
            }
        }
        // Sync Doctors
        // $this->syncDoctors();
    }
    /**
     * Sync Doctor Details from HMH Server
     *
     * @author Tanmaya
     * @date 03 Apr 2021
     * @return none
     */
    public function syncDoctors()
    {
        $doctors = [];
        $userId = User::where('role', 'admin')->first()->id;
        $hospitals = Hospital::with('departments', 'departments.department')
            // ->where('id', '13')
            ->select('id', 'code')
            ->get();

        if (!$hospitals->isEmpty()) {
            $combinations = [];
            $syncData = [];
            foreach ($hospitals as $key => $hospital) {
                $selHospitalCode = $hospital->code;
                foreach ($hospital->departments as $key => $department) {
                    $selDepartment = $department->department->ref_id;
                    $combinations = [
                        'dept_id' => $department->department->id,
                        'dept_ref_id' => $selDepartment,
                        'hospital_id' => $hospital->id,
                        'hospital_code' => $selHospitalCode,
                    ];

                    $syncData = [
                        'user_id' => $userId,
                        'information' => $combinations,
                    ];

                    \App\Jobs\OnSubmitJob::dispatch([
                        'data' => $syncData,
                        'type' => 'doctor_sync',
                    ])->delay(now());
                }
            }
        }
        // Sync Doctor's Shift Details
        // $this->doctorShift();
        // return true;
    }

    /**
     * Dcotor Data synced code
     * Fired up from "OnSubmitJob" job. This Job fires up by "syncDoctors()" method
     */
    public function updateDoctorSync($userId, $information)
    {
        $response = Http::asForm()->post($this->ociApp . $this->doctorsApi, [
            'hospital' => $information['hospital_code'],
            'speciality' => $information['dept_ref_id'],
        ]);
        if ($response->status() == 200) {
            $doctorsResponse = \json_decode($response->body());
            foreach ($doctorsResponse as $key => $doctorResp) {
                $doctorData = [
                    'user_id' => $userId,
                    'ref_id' => $doctorResp->DOCTOR_ID,
                    'name_en' => $this->casing($doctorResp->DOCTOR_NAME_EN),
                    'name_ar' => $this->casing($doctorResp->DOCTOR_NAME_AR),
                    'hospital_id' => $information['hospital_id'],
                    'department_id' => $information['dept_id'],
                ];
                if (Doctor::where(['ref_id' => intval($doctorResp->DOCTOR_ID)])->count() == 0) {
                    // dump($doctorData);
                    Doctor::create($doctorData);
                } else {
                    // Update Doctors
                    Doctor::where(['ref_id' => $doctorResp->DOCTOR_ID])->update([
                        'user_id' => $userId,
                        'name_en' => $this->casing($doctorResp->DOCTOR_NAME_EN),
                        'name_ar' => $this->casing($doctorResp->DOCTOR_NAME_AR),
                        'hospital_id' => $information['hospital_id'],
                        'department_id' => $information['dept_id'],
                    ]);
                    $this->doctorShift($doctorResp->DOCTOR_ID);
                }
            }
        }
    }

    public function doctorShift($refId)
    {
        $time = date('H:i:s');
        $allSchedules = [];
        $days = days();
        $doctors = Doctor::where(['ref_id' => $refId])
            ->with('hospital', 'department')
            ->get();
        foreach ($doctors as $doctor) {
            $response = Http::asForm()->post($this->ociApp . $this->doctorScheduleApi, [
                'hospital' => $doctor->hospital->code,
                'speciality' => $doctor->department->ref_id,
                'doctor_id' => $doctor->ref_id,
                'from' => strtolower(date('d/M/Y')), //strtolower(date('d-M-Y', strtotime("-7 days"))),
                'to' => strtolower(date('d/M/Y', strtotime("+7 days"))),
            ]);
            if ($response->status() == 200) {
                $getSchedules = json_decode($response->body());
                $doctorSlots = [];
                foreach ($getSchedules as $key => $shift) {
                    $startDateTime = $this->fixSlotDateFormat($shift->SHIFT_START_TIME);
                    $endDateTime = $this->fixSlotDateFormat($shift->SHIFT_END_TIME);
                    $doctorSlots[] = [
                        'doctor_id' => $doctor->id,
                        'date' => $startDateTime['date'],
                        'day' => \Carbon\Carbon::parse($startDateTime['date'])->format('l'),
                        'duty_type' => date("h:i A", strtotime($startDateTime['time'])) . " - " . date("h:i A", strtotime($endDateTime['time'])),
                        'shift_name' => date("h:i A", strtotime($startDateTime['time'])) . " - " . date("h:i A", strtotime($endDateTime['time'])),
                        'from' => $startDateTime['time'],
                        'to' => $endDateTime['time'],
                        'slot_duration' => $shift->TIME_SLOT_MINUTES,
                        'schedule_serial' => $shift->SCHEDULE_SERIAL
                    ];
                }
                // Preparing to Log into DB
                /*$apiData = json_encode([
                    'hospital' => $doctor->hospital->code,
                    'speciality' => $doctor->department->ref_id,
                    'doctor_id' => $doctor->ref_id,
                    'from' => strtolower(date('d/M/Y')),
                    'to' => strtolower(date('d/M/Y', strtotime("+7 days"))),
                ]);
                DB::insert('insert into `get_logs` (module, api_data, logs) values (?,?,?)', ['doctors', $apiData, json_encode($doctorSlots)]);*/
                // End

                DoctorAvailability::whereDoctorId($doctor->id)->delete();
                DoctorAvailability::insert($doctorSlots);
            }
        }

        echo $time . " >> " . date('H:i:s');
    }

    /**
     * Sync Doctor's Total Shifts
     */
    public function shiftList()
    {
        $hospitals = Hospital::pluck('code', 'id');
        $shiftDetails = [];
        foreach ($hospitals as $id => $code) {
            $response = Http::asForm()->post($this->ociApp . $this->shiftLists, [
                'hospital' => $code,
            ]);
            if ($response->status() == 200) {
                $shifts = \json_decode($response->body(), true);
                foreach ($shifts as $key => $shift) {
                    $checkShift = DoctorShift::where(['ref_id' => $shift['SHIFT_ID'], 'hospital_id' => $id]);
                    if ($checkShift->count() == 0) {
                        // Add
                        $save = new DoctorShift([
                            'ref_id' => $shift['SHIFT_ID'],
                            'name' => $shift['SHIFT_NAME'],
                            'hospital_id' => $id,
                        ]);
                        $save->save();
                    } else {
                        // Update
                        $update = DoctorShift::where(['ref_id' => $shift['SHIFT_ID']])
                            ->update([
                                'name' => $shift['SHIFT_NAME'],
                            ]);
                    }
                }
            }
        }

        echo "Task Completed! \n";
    }

    /**
     * Get Doctor's Cloud ID from HMH Ref ID
     */
    public static function doctorId($refId)
    {
        $doctor = Doctor::whereRefId($refId)->first();
        return !empty($doctor->id) ? $doctor->id : 0;
    }

    /**
     * Convert the Date format compatible with PHP
     */
    public function fixSlotDateFormat($date)
    {
        $dateTime = [];
        $separateDateTime = explode(" ", $date);
        $newDate = str_replace("/", "-", $separateDateTime[0]);
        return [
            'date' => date('Y-m-d', strtotime($newDate)),
            'time' => $separateDateTime[1],
        ];
    }
}
