<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Hospital;
use App\Models\MrnNumber;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

class SyncController extends Controller
{
    protected $ociApp;

    public function __construct()
    {
        $this->ociApp = env('OCI_APP');
    }
    /**
     * Update patient data to the Oracle server
     */
    public function uploadPatients($type)
    {
        $data = [];
        $update = 0;
        $notify['data'] = "Data SYNC was done between Cloud and Local Server";
        $notify['start_at'] = \Carbon\Carbon::now();
        $patients = Patient::with('user', 'hospital')->get();
        foreach ($patients as $key => $patient) {
            $data[$key] = [
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
                'ID_NUMBER' => (int)$patient['national_id'],
                'IDIN_TYPE' => $patient['national_id_type'],
                'NAT_CODE' => $patient['nationality'],
                'REL_CODE' => (int)$patient['religion'],
                'COUNTRY_CODE' => $patient['country_id'],
                'CITY' => $patient['city_id'],
                'DATEOFBIRTH' => date('Y-m-d H:i:s', strtotime($patient['dob'])),
                'LAST_SYNC_DATE' => datetoDB($patient['created_at']),
                'EMAIL' => $patient['user']->email,
                'ORIGIN' => 'CLOUD',
                'PATIENT_TYPE' => 'NEW',
            ];

            if ($type == 'update') {
                // update Records >>>>>> There is some problem in udate
                $response = Http::asForm()->post($this->ociApp . '/patient/sync/update', [
                    'data' => json_encode($data[$key]),
                    'condition' => \json_encode(['id' => $patient['id'], 'ORIGIN' => 'CLOUD']),
                ]);
                if ($response->status() == 200) {
                    $update = 1;
                }
            }
        }
        if ($type == "update" && !empty($update)) {
            $notify['type'] = "Updated existing records";
            $notify['end_at'] = \Carbon\Carbon::now();
            $this->addToHistory($notify);
        }
        if ($type == 'save') {
            // add new records
            $response = Http::asForm()->post($this->ociApp . '/patient/sync/upload', [
                'data' => json_encode($data),
            ]);
            if ($response->status() == 200) {
                $notify['type'] = "Added new records";
                $notify['end_at'] = \Carbon\Carbon::now();
                $this->addToHistory($notify);
            }
        }
    }

    /**
     * Add Sync description to notification module So that
     * Admin can see all ongoing task
     */
    public function addToHistory($notify = [])
    {
        $user = \App\Models\User::find(1);
        Notification::send($user, new \App\Notifications\TaskComplete($notify));
    }

    /**
     * Get MRN numbers from HMH database and update in cloud server
     * 
     * @author Tanmaya
     * @date Mar 29 2021
     * @return any
     */
    public function getMrnNumbers()
    {
        $patients = Patient::where('has_mrn', 0)
            ->select('phone', 'national_id', 'national_id_type')
            ->get()
            ->toJson();
        $response = Http::asForm()->post($this->ociApp . '/cloud/sync/mrn', [
            'data' => $patients,
        ]);
        if ($response->status() == 200) {
            // echo $response->body();
            $getResponse = json_decode($response->body());
            // dd($getResponse);
            if (!empty($getResponse->data)) {
                $ociResponse = $getResponse->data;
                foreach ($ociResponse as $key => $ociResp) {
                    $selectPatiant = Patient::where([
                        'national_id' => $ociResp->ID_NUMBER,
                        'national_id_type' => $ociResp->IDIN_TYPE
                    ]);
                    if ($selectPatiant->count() > 0) {
                        $patientData = $selectPatiant->first();
                        $patientId = $patientData->id;
                        $hospitalId = 0;
                        
                        try {
                            $getHospital = Hospital::where('code', $ociResp->HOSPITAL_CODE)
                                ->first();
                            $hospitalId = $getHospital->id;
                            $updateMrn = [
                                'patient_id' => $patientId,
                                'hospital_id' => $hospitalId,
                                'mrn' => $ociResp->MRN
                            ];
                            $checkMrnExist = MrnNumber::where([
                                'patient_id' => $patientId,
                                'hospital_id' => $hospitalId
                            ])->count();
                            if ($checkMrnExist == 0) {
                                MrnNumber::insert($updateMrn);
                                $selectPatiant->refresh();
                                // $$selectPatiant->update(['has_mrn' => 1]);
                            }
                            // Opt out the patient
                            Patient::where('id', $patientId)->update(['has_mrn' => 1]);
                        } catch (\Exception $e) {
                            //throw $th;
                        }
                    }
                }
            }
        }
    }
}
