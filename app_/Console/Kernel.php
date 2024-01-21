<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sync:fiveminute')
            ->{env('SYNC_DURATION')}()
            // ->emailOutputTo('tanmayapatra09@gmail.com')
            ->withoutOverlapping();
        $schedule->command('sync:weekly')
            // ->mondays()
            // ->wednesdays()
            // ->fridays()
            // ->sundays()
            ->emailOutputTo('tanmayasmtpdev@gmail.com');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
