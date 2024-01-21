<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Sync\DoctorSync;

class WeeklySync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Records with HMH server in every weekend';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Sync Doctor's Shifts in Each hospital
        $doctor = new DoctorSync();
        $doctor->shiftList();
        $doctor->syncDepartments();
        $doctor->syncDoctors();
        return true;
    }
}
