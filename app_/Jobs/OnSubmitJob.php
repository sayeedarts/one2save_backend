<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
// use Illuminate\Queue\Middleware\WithoutOverlapping;

class OnSubmitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        if ($this->params['type'] == "sick_leave") {
            $sync = new \App\Http\Controllers\Sync\PatientSync();
            echo $sync->uploadSickLeaveRequest($this->params['data']);
        } else if ($this->params['type'] == "booking_upload") {
            $sync = new \App\Http\Controllers\Sync\PatientSync();
            $sync->uploadBookingDetails($this->params['data']);
        } else if ($this->params['type'] == "patient_save") {
            $sync = new \App\Http\Controllers\Site\SyncController();
            $sync->uploadPatients('save');
            echo "New patients added successfully! \n";
        } else if ($this->params['type'] == "patient_update") {
            // $sync = new \App\Http\Controllers\Site\SyncController();
            // $sync->uploadPatients('update');
            $sync = new \App\Http\Controllers\Sync\PatientSync();
            $sync->patientDataUpdate($this->params['user_id']);
            echo "New patients updated successfully! \n";
        } else if ($this->params['type'] == "mrn_sync") {
            $sync = new \App\Http\Controllers\Site\SyncController();
            $sync->getMrnNumbers();
        } else if ($this->params['type'] == "doctor_sync") {
            $sync = new \App\Http\Controllers\Sync\DoctorSync();
            $sync->updateDoctorSync(
                $this->params['data']['user_id'],
                $this->params['data']['information'],
            );
        }
    }

    // public function middleware()
    // {
    //     return [new WithoutOverlapping($this->params['user_id'])];
    // }
}
