<?php

namespace App\Http\Controllers\Sync;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class MasterDataSync extends Controller
{
    protected $ociApp;
    protected $specialityApi = '/hmh/speciality/details';
    protected $doctorsApi = '/hmh/doctor/details';
    protected $doctorScheduleApi = '/hmh/doctor/schedule';
    protected $patientDetails = '/hmh/patient/details';
    protected $shiftLists = '/hmh/shifts/all';
    // protected $patientBookingDetails = '/hmh/patient/booking-time';
    protected $patientBookingDetails = '/hmh/patient/booking-details';
    protected $patientLabDetails = '/hmh/patient/lab-details';
    protected $patientRadioDetails = '/hmh/patient/radiology';
    protected $patientMedDetails = '/hmh/patient/med-plan';
    protected $patientVisitList = '/hmh/patient/visits';
    public $uploadBookingInfo = '/hmh/patient/booking-details/upload';
    public $uploadSickleaveInfo = '/hmh/patient/sickleave-request/upload';
    public $getSickleaveDetails = '/hmh/patient/sickleave/report';

    public function __construct()
    {
        $this->ociApp = env('OCI_APP');
    }

    public function getPatientDetails($hospitalId, $marnNumber)
    {
        $response = Http::asForm()->post($this->ociApp . $this->patientDetails, [
            'hospital' => $hospitalId,
            'patient' => $marnNumber,
        ]);
        if ($response->status() == 200) {
            $patientDetails = \json_decode($response->body());
            return $patientDetails;
        }
    }

    /**
     * Get Patient's booking Time
     */
    public function getPatientBookingDetails()
    {
        $getpatients = Patient::get();

        dd($getpatients->toArray());

        exit;
        $response = Http::asForm()->post($this->ociApp . $this->patientDetails, [
            'hospital' => $hospitalId,
            'patient' => $marnNumber,
        ]);
        if ($response->status() == 200) {
            $patientDetails = \json_decode($response->body());
            return $patientDetails;
        }
    }

    /**
     * Get the last seven Days
     */
    public function lastSeven()
    {
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $date[] = \Carbon\Carbon::today()->addDays($i)->format('Y-m-d');
        }

        return $date;
    }

    /**
     * Sterilize and capitalize each letter of every word
     */
    public function casing($string)
    {
        return ucwords(strtolower(trim($string)));
    }

    public function shiftName($time)
    {
        if (date("H", strtotime($time)) < 12) {
            return "Morning";
        } elseif (date("H", strtotime($time)) > 11 && date("H", strtotime($time)) < 18) {
            return "Afternoon";
        } elseif (date("H", strtotime($time)) > 17) {
            return "Evening";
        }
    }

    public function clearTrash()
    {
        $tables = ['notifications', 'get_logs'];
        foreach ($tables as $key => $table) {
            DB::table($table)->truncate();
        }
    }
}
