<?php

namespace App\Http\Controllers\Sync;

use App\Http\Controllers\Sync\DoctorSync;
use App\Http\Controllers\Sync\MasterDataSync;
use App\Models as ApMod;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\MrnNumber;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\PatientTest;
use App\Models\PatientTestDetail;
use App\Models\Report;
use Illuminate\Support\Facades\Http;

class PatientSync extends MasterDataSync
{
    /**
     * Update a patient's data at Int. Server after update in Cloud server
     *
     * @param $userId numeric User's Login Id
     *
     * @author Tanmaya <t@gmail.com>
     * @date May 23 2021
     * @return none
     */
    public function patientDataUpdate($userId)
    {
        $patient = Patient::whereUserId($userId)->with('user', 'hospital')->first();
        $data = [
            'ID' => $patient['id'],
            'F_NAME' => $patient['firstname_ar'],
            'S_NAME' => $patient['secondname_ar'],
            'T_NAME' => $patient['thirdname_ar'],
            'L_NAME' => $patient['lastname_ar'],
            'F_NAME_EN' => $patient['firstname'],
            'S_NAME_EN' => $patient['secondname'],
            'T_NAME_EN' => $patient['thirdname'],
            'L_NAME_EN' => $patient['lastname'],
            'HOSP_ID' => !empty($patient['hospital']->id) ? $patient['hospital']->id : '',
            'PATIENT_TEL' => $patient['phone'],
            'GENDERCODE' => $patient['gender'],
            'ID_NUMBER' => (int) $patient['national_id'],
            'IDIN_TYPE' => $patient['national_id_type'],
            'NAT_CODE' => $patient['nationality'],
            'REL_CODE' => (int) $patient['religion'],
            'COUNTRY_CODE' => $patient['country_id'],
            'CITY' => $patient['city_id'],
            'DATEOFBIRTH' => date('Y-m-d H:i:s', strtotime($patient['dob'])),
            'LAST_SYNC_DATE' => datetoDB($patient['created_at']),
            'EMAIL' => $patient['user']->email,
            'ORIGIN' => 'CLOUD',
            'PATIENT_TYPE' => 'NEW',
        ];
        $response = Http::asForm()->post($this->ociApp . '/patient/sync/update', [
            'data' => json_encode($data),
            'condition' => \json_encode(['id' => $patient['id'], 'ORIGIN' => 'CLOUD']),
        ]);
        if ($response->status() == 200) {
            $update = 1;
            // echo $response->body(); exit;
        }
    }
    /**
     * Get Patients booking time from HMH end
     */
    public function patientsBookingTime($userId)
    {
        $bookingData = [];
        $patientDetails = Patient::whereUserId($userId);

        // Sync Booking data in 15min Gap
        // $lastSyncDate = session('last_booking_sync_date');
        // $doSyncBooking = false;
        // if (!empty($lastSyncDate)) {
        //     $lastSyncDate = \Carbon\Carbon::parse($lastSyncDate);
        //     $currentDate = \Carbon\Carbon::parse(now());
        //     $syncTimeGap = $currentDate->diffInMinutes($lastSyncDate, true);
        //     $doSyncBooking = $syncTimeGap > 10 ? true : false;
        // } else {
        //     $doSyncBooking = true;
        // }

        if ($patientDetails->count() > 0/*&& $doSyncBooking*/) {
            $getDetails = $patientDetails->with('mrns', 'mrns.hospital')->first()->toArray();
            foreach ($getDetails['mrns'] as $mrn) {
                $response = Http::asForm()->post($this->ociApp . $this->patientBookingDetails, [
                    'hospital' => $mrn['hospital']['code'],
                    'patient' => $mrn['mrn'],
                    'from' => '01-jan-2019', //strtolower(date('d-M-Y', strtotime("-7 days"))),
                    'to' => strtolower(date('d-M-Y', strtotime("+7 days"))),
                ]);
                if ($response->status() == 200) {
                    $patientDetails = \json_decode($response->body());
                    if (!empty($patientDetails)) {
                        foreach ($patientDetails as $key => $detail) {
                            $speciality = Department::whereRefId($detail->SPECIALTY_ID)->first();
                            $doctor = Doctor::whereRefId($detail->DOCTOR_ID)->first();
                            // Convert to correct date format
                            $cleanDate = cleanOrDate($detail->RES_DATE);

                            $bookingData = [
                                'hospital_id' => $mrn['hospital']['id'],
                                'department_id' => $speciality->id,
                                'doctor_id' => !empty($doctor->id) ?? 0,
                                'date' => $cleanDate['date'],
                                'time' => $cleanDate['time'],
                                'user_id' => $userId,
                                'payment_type' => 'cash',
                                'source' => 1, // for external source
                            ];
                            $bookingCondition = [
                                'user_id' => $userId,
                                'department_id' => $speciality->id,
                                'doctor_id' => !empty($doctor->id) ?? 0,
                                'date' => $bookingData['date'],
                                // 'time' => $bookingData['time']
                            ];
                            $checkBooking = Appointment::where($bookingCondition);
                            if ($checkBooking->count() == 0) {
                                $booking = new Appointment($bookingData);
                                $booking->save();
                            } else {
                                /*Appointment::where($bookingCondition)
                            ->update($bookingData);*/
                            }
                        }
                    }
                }
            }
            // session()->forget('last_booking_sync_date');
            // session(['last_booking_sync_date' => now()]);
        }
    }

    public function uploadBookingDetails($bookingData)
    {
        $response = Http::asForm()->post($this->ociApp . $this->uploadBookingInfo, $bookingData);
        if ($response->status() == 200) {
            $upload = \json_decode($response->body());
            if (!empty($upload->status) && $upload->status == 1) {
                return true;
            }
        }
        return false;
    }

    public function uploadSickLeaveRequest($visitData)
    {
        $response = Http::asForm()->post($this->ociApp . $this->uploadSickleaveInfo, $visitData);
        if ($response->status() == 200) {
            $upload = json_decode($response->body());
            return $upload->message . "\n";
        }
        return false;
    }

    public function getPatientSickLeaveReport($userId = 39)
    {
        $getVisitDetails = PatientVisit::where(['user_id' => $userId, 'leave_requested' => 1]);
        if ($getVisitDetails->count() > 0) {
            $getVisits = $getVisitDetails->with('doctor', 'doctor.hospital')->get();
            foreach ($getVisits as $visit) {
                $response = Http::asForm()->post($this->ociApp . $this->getSickleaveDetails, [
                    'hospital' => $visit->doctor->hospital->code,
                    'patient' => $visit->patient_id,
                    'visit_id' => $visit->ref_id,
                ]);
                if ($response->status() == 200) {
                    $sickLeaveDetails = \json_decode($response->body());
                    if (!empty($sickLeaveDetails)) {
                        foreach ($sickLeaveDetails as $key => $sickLeave) {
                            $hospitalData = Hospital::whereCode(trim($sickLeave->HOSP_ID))->first();
                            $doctorData = ApMod\Doctor::whereRefId(trim($sickLeave->DOCTOR_ID))->first();
                            $reportData = [
                                'hospital_id' => $hospitalData->id,
                                'doctor_id' => $doctorData->id,
                                'patient_id' => $visit->patient_id,
                                'mrn' => trim($sickLeave->PATIENTID),
                                'visit_id' => $sickLeave->VISIT_ID,
                                'visit_date' => $this->fixLabDateFormat($sickLeave->VISIT_START_DATE),
                            ];
                            $checkReport = Report::where($reportData);
                            $reportId = 0;
                            if ($checkReport->count() == 0) {
                                $saveReport = new Report($reportData);
                                $saveReport->save();
                                $reportId = $saveReport->id;
                            } else {
                                $getReport = $checkReport->first();
                                $reportId = $getReport->id;
                            }

                            // Step 2: Patient Lab Data
                            $sickLeaveData = [
                                'report_id' => $reportId,
                                'admission_date' => $this->fixLabDateFormat($sickLeave->ADMISSION_DATE),
                                'discharge_date' => !empty($sickLeave->DISCHARGE_DATE) ? $this->fixLabDateFormat($sickLeave->DISCHARGE_DATE) : null,
                                'vacation_from' => $this->fixLabDateFormat($sickLeave->VACATION_FROM_DATE),
                                'vacation_to' => $this->fixLabDateFormat($sickLeave->VACATION_TO_DATE),
                                'vacation_days' => $sickLeave->VACATION_DAY_NO,
                                'diagnosis' => $sickLeave->DIAGNOSIS,
                                'remarks' => $sickLeave->MEDICAL_COMMENT,
                            ];

                            $checkPatTest = \App\Models\ReportSickLeave::where($sickLeaveData);
                            $patientTestId = 0;
                            if ($checkPatTest->count() == 0) {
                                $saveTest = new \App\Models\ReportSickLeave($sickLeaveData);
                                $saveTest->save();
                            } else {
                                $patientTestData = $checkPatTest->first();
                            }

                        }
                    }
                }
            }
        }
    }
    /**
     * Fetch Laboratory details from the HMH end for SYNC
     */
    public function labDetails($userId)
    {
        // Sync Booking data in 15min Gap
        /*$lastLabSyncDate = session('last_lab_details_sync_date');
        $doSyncLabReeport = false;
        if (!empty($lastLabSyncDate)) {
        $lastLabSyncDate = \Carbon\Carbon::parse($lastLabSyncDate);
        $currentDate = \Carbon\Carbon::parse(now());
        $syncTimeGap = $currentDate->diffInMinutes($lastLabSyncDate, true);
        $doSyncLabReeport = $syncTimeGap > 10 ? true : false;
        } else {
        $doSyncLabReeport = true;
        }*/
        $getPatient = Patient::whereUserId($userId);
        if ($getPatient->count() > 0/*&& $doSyncLabReeport*/) {
            $patientDetails = $getPatient->first();
            $response = Http::asForm()->post($this->ociApp . $this->patientLabDetails, [
                'hospital' => '%',
                'patient' => $patientDetails->id,
                'from' => '01-jan-2019', //strtolower(date('d-M-Y', strtotime("-7 days"))),
                'to' => strtolower(date('d-M-Y')),
            ]);
            if ($response->status() == 200) {
                $patLabDetails = \json_decode($response->body());
                if (!empty($patLabDetails)) {
                    foreach ($patLabDetails as $key => $lab) {
                        $hospitalData = Hospital::whereCode(trim($lab->HOSPITAL_CODE))->first();
                        $doctorData = ApMod\Doctor::whereRefId(trim($lab->DOCTOR_ID))->first();
                        // Step 1 : Report table
                        $reportData = [
                            'hospital_id' => $hospitalData->id,
                            'doctor_id' => !empty($doctorData->id) ? $doctorData->id : null,
                            'patient_id' => $patientDetails->id,
                            'mrn' => $lab->MRN,
                            'visit_id' => $lab->VISIT_ID,
                            'visit_date' => $this->fixLabDateFormat($lab->VISITDATE),
                        ];
                        $checkReport = Report::where($reportData);
                        $reportId = 0;
                        if ($checkReport->count() == 0) {
                            $saveReport = new Report($reportData);
                            $saveReport->save();
                            $reportId = $saveReport->id;
                        } else {
                            $getReport = $checkReport->first();
                            $reportId = $getReport->id;
                        }

                        // Step 2: Patient Lab Data
                        $patientTest = [
                            'report_id' => $reportId,
                            // 'patient_id' => $patientDeyails->id,
                            // // 'hospital_id' => $hospital->id,
                            // 'mrn' => $lab->MRN,
                            // 'visit_id' => $lab->VISIT_ID,
                            'request_id' => $lab->REQ_ID,
                            'service_id' => $lab->SERVICE_ID,
                            'lab_profile' => $lab->LABPROFILE,
                            // 'created_at' => $this->fixLabDateFormat($lab->VISITDATE),
                        ];

                        $checkPatTest = PatientTest::where($patientTest);
                        $patientTestId = 0;
                        if ($checkPatTest->count() == 0) {
                            $saveTest = new PatientTest($patientTest);
                            $saveTest->save();
                            $patientTestId = $saveTest->id;
                        } else {
                            $patientTestData = $checkPatTest->first();
                            $patientTestId = $patientTestData->id;
                        }

                        // Step 3 : Patient Lab Details
                        $patientTestDetails = [
                            'patient_test_id' => $patientTestId,
                            'lab_test_name' => $lab->LABTESTNAME,
                            'lab_result' => $lab->LABRESULT,
                            'lab_units' => $lab->LABUNITS,
                            'lab_low' => $lab->LABLOW,
                            'lab_high' => $lab->LABHIGH,
                            'lab_section' => $lab->LABSECTION,
                            'tested_at' => $this->fixLabDateFormat($lab->VISITDATE),
                        ];
                        $saveTestDetails = new PatientTestDetail($patientTestDetails);
                        $saveTestDetails->save();
                    }
                }
                // session()->forget('last_lab_details_sync_date');
                // session(['last_lab_details_sync_date' => now()]);
            }
        }
        echo "Lab Details SYNCed \n";
    }

    /**
     * Sync Radiology details of patients
     */
    public function radiologyDetails($patientId = "")
    {
        $getPatient = Patient::whereUserId($patientId);
        if ($getPatient->count() > 0) {
            $patientDetails = $getPatient->first();
            $response = Http::asForm()->post($this->ociApp . $this->patientRadioDetails, [
                'hospital' => '%',
                'patient' => $patientDetails->id,
                'from' => '01-jan-2019', //strtolower(date('d-M-Y', strtotime("-7 days"))),
                'to' => strtolower(date('d-M-Y')),
            ]);

            if ($response->status() == 200) {
                $radiologyDetails = \json_decode($response->body());
                if (!empty($radiologyDetails)) {
                    foreach ($radiologyDetails as $key => $radio) {
                        $hospitalData = Hospital::whereCode(trim($radio->HOSPITAL_CODE))->first();
                        $doctorData = ApMod\Doctor::whereRefId(trim($radio->DOCTOR_ID))->first();
                        $reportData = [
                            'hospital_id' => $hospitalData->id,
                            'doctor_id' => $doctorData->id,
                            'patient_id' => $patientDetails->id,
                            'mrn' => trim($radio->MRN),
                            'visit_id' => $radio->VISIT_ID,
                            'visit_date' => $this->fixLabDateFormat($radio->VISITDATE),
                        ];
                        $checkReport = Report::where($reportData);
                        $reportId = 0;
                        if ($checkReport->count() == 0) {
                            $saveReport = new Report($reportData);
                            $saveReport->save();
                            $reportId = $saveReport->id;
                        } else {
                            $getReport = $checkReport->first();
                            $reportId = $getReport->id;
                        }

                        // Step 2: Patient Lab Data
                        $radiologyData = [
                            'report_id' => $reportId,
                            'service_code' => $radio->SERVICECODE,
                            'service_title' => $radio->SERVICEDESCRIPTION,
                            'result' => $radio->RADIOLOGYRESULT,
                        ];

                        $checkPatTest = \App\Models\ReportRadiology::where($radiologyData);
                        $patientTestId = 0;
                        if ($checkPatTest->count() == 0) {
                            $saveTest = new \App\Models\ReportRadiology($radiologyData);
                            $saveTest->save();
                        } else {
                            $patientTestData = $checkPatTest->first();
                        }
                    }
                }
            }
        }
    }

    /**
     * Sync Radiology details of patients
     */
    public function medicineDetails($patientId = "")
    {
        $getPatient = Patient::whereUserId($patientId);
        if ($getPatient->count() > 0) {
            $patientDetails = $getPatient->first();
            $response = Http::asForm()->post($this->ociApp . $this->patientMedDetails, [
                'hospital' => '%',
                'patient' => $patientDetails->id,
                'from' => '01-jan-2019', //strtolower(date('d-M-Y', strtotime("-7 days"))),
                'to' => strtolower(date('d-M-Y')),
            ]);

            if ($response->status() == 200) {
                $medicineDetails = json_decode($response->body());
                // $sampleDataArr = json_decode($sampleData, true);
                // $medicineDetails = json_decode(json_encode($sampleDataArr), FALSE);
                // dd($medicineDetails);
                if (!empty($medicineDetails)) {
                    foreach ($medicineDetails as $key => $medicine) {
                        $hospital = ApMod\Hospital::whereCode(trim($medicine->HOSPITAL_CODE))->first();
                        $visitDetails = ApMod\PatientVisit::where([
                            'mrn' => $medicine->MRN, 'ref_id' => $medicine->VISIT_ID,
                        ])->first();
                        $reportData = [
                            'hospital_id' => $hospital->id,
                            'doctor_id' => $visitDetails->doctor_id,
                            'patient_id' => $patientDetails->id,
                            'mrn' => trim($medicine->MRN),
                            'visit_id' => $medicine->VISIT_ID,
                            'visit_date' => $this->fixLabDateFormat($visitDetails->date),
                        ];
                        $checkReport = Report::where($reportData);
                        $reportId = 0;
                        if ($checkReport->count() == 0) {
                            $saveReport = new Report($reportData);
                            $saveReport->save();
                            $reportId = $saveReport->id;
                        } else {
                            $getReport = $checkReport->first();
                            $reportId = $getReport->id;
                        }

                        // Step 2: Patient Medicine Data
                        $medicineData = [
                            'report_id' => $reportId,
                            'medplan_no' => $medicine->MEDPLAN_NO,
                            'medplan_date' => $this->fixLabDateFormat($medicine->MEDPLAN_DATE),
                            'item_code' => $medicine->ITEM_CODE,
                            'item_name' => $medicine->ITEM_NAME,
                            'notes' => $medicine->NOTES_EN,
                            'notes_ar' => $medicine->NOTES_AR,
                            'remarks' => $medicine->REMARKS,
                        ];

                        $checkPatTest = \App\Models\ReportMedicine::where($medicineData);
                        $patientTestId = 0;
                        if ($checkPatTest->count() == 0) {
                            $saveTest = new \App\Models\ReportMedicine($medicineData);
                            $saveTest->save();
                        } else {
                            $patientTestData = $checkPatTest->first();
                        }
                    }
                }
            }
        }
        echo "Medicine Records successfully updated! \n";
    }

    /**
     * Get Patient Visit Details from the HMH Database
     */
    public function getPatientVisitDetails($userId)
    {
        $getPatient = Patient::whereUserId($userId);
        if ($getPatient->count() > 0) {
            $patientDetails = $getPatient->first();
            $hospitals = Hospital::pluck('code', 'id');
            $shiftDetails = [];
            foreach ($hospitals as $id => $code) {
                $response = Http::asForm()->post($this->ociApp . $this->patientVisitList, [
                    'hospital' => $code,
                    'patient' => $patientDetails->id,
                    'from' => '01-jan-2019', //strtolower(date('d-M-Y', strtotime("-7 days"))),
                    'to' => strtolower(date('d-M-Y')),
                ]);
                if ($response->status() == 200) {
                    $visitData = \json_decode($response->body());
                    if (!empty($visitData)) {
                        foreach ($visitData as $key => $visit) {
                            $info = $condition = [
                                'ref_id' => $visit->VISIT_ID,
                                'date' => $this->fixLabDateFormat($visit->VISITDATE),
                                'mrn' => $visit->MRN,
                            ];
                            $checkVisit = \App\Models\PatientVisit::where($condition);
                            $getDoctorId = DoctorSync::doctorId($visit->DOCID);
                            $info += [
                                'patient_id' => $patientDetails->id,
                                'user_id' => $userId,
                                'doctor_id' => !empty($getDoctorId) ? $getDoctorId : 0,
                                'doctor_ref_id' => $visit->DOCID,
                                'doctor_name' => ucfirst(strtolower($visit->DOC_NAME)),
                            ];
                           
                            if ($checkVisit->count() == 0) {
                                // Insert
                                $save = new \App\Models\PatientVisit($info);
                                $save->save();
                            } else {
                                // Update
                                $checkVisit->update($info);
                            }
                        }
                    }
                }
            }
        }
        echo "Visit Details SYNCed. \n";
    }

    public function mrnUserId($mrnNum)
    {
        $getMrn = MrnNumber::whereMrn($mrnNum)->with('patient_user_id')->first();
        if (!empty($getMrn->patient_user_id->user_id)) {
            return $getMrn->patient_user_id->user_id;
        }
        return 0;
    }

    /**
     * This method converts date format like "05-MAY-19 12.00.00.000 AM"
     * to a workable php date format
     */
    public function fixLabDateFormat($date)
    {
        $separateDateTime = explode(" ", $date);
        $otherParts = "";
        for ($i = 1; $i < count($separateDateTime); $i++) {
            $otherParts .= " " . $separateDateTime[$i];
        }
        $formattedDate = date("Y-m-d", strtotime($separateDateTime[0]));
        $formattedDate .= " " . date("H:i", strtotime($otherParts));
        return $formattedDate;
    }
}
