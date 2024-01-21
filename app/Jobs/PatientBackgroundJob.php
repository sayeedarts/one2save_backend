<?php

namespace App\Jobs;

use App\Http\Controllers\Sync\PatientSync;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PatientBackgroundJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->params['type'] == "patient_visits") {
            $patientSync = new PatientSync();
            $patientSync->getPatientVisitDetails($this->params['user_id']);
        } else if ($this->params['type'] == "lab_reports") {
            $sync = new PatientSync();
            $sync->labDetails($this->params['user_id']);
        } else if ($this->params['type'] == "radio_reports") {
            $sync = new PatientSync();
            $sync->radiologyDetails($this->params['user_id']);
        } else if ($this->params['type'] == "patient_booking") {
            $sync = new PatientSync();
            $sync->patientsBookingTime($this->params['user_id']);
        } else if ($this->params['type'] == "patient_medicine") {
            $sync = new PatientSync();
            $sync->medicineDetails($this->params['user_id']);
        }
    }
}
