<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Site\SyncController;

class FiveMinuteSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:fiveminute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Records with HMH Server in every 5 min or 10mins';

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
        $sync = new SyncController();
        $sync->uploadPatients('update');
        $sync->uploadPatients('save');
        $sync->getMrnNumbers();
        return "Last action was done successfully! \n";
        // \App\Models\Patient::where('id', 1)->update(['secondname' => 'FROM CRON >' . date('h:i:s')]);
    }
}
