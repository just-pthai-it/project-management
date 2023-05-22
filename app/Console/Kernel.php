<?php

namespace App\Console;

use App\Jobs\UpdateBehindScheduleProjectStatus;
use App\Jobs\UpdateBehindScheduleTaskStatus;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule (Schedule $schedule) : void
    {
        $schedule->job(new UpdateBehindScheduleProjectStatus())->timezone('Asia/Ho_Chi_Minh')->dailyAt('00:00');
        $schedule->job(new UpdateBehindScheduleTaskStatus())->hourly();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands ()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
