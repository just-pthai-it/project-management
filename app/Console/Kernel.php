<?php

namespace App\Console;

use App\Jobs\NotifyBehindScheduleProject;
use App\Jobs\NotifyBehindScheduleTask;
use App\Jobs\NotifyDeadlineProject;
use App\Jobs\NotifyDeadlineTask;
use App\Jobs\Test;
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
        $schedule->job(new UpdateBehindScheduleProjectStatus())->timezone('Asia/Ho_Chi_Minh')->dailyAt('07:00');
        $schedule->job(new UpdateBehindScheduleTaskStatus())->hourly();
        $schedule->job(new NotifyDeadlineProject('daily'))->timezone('Asia/Ho_Chi_Minh')->dailyAt('07:00');
        $schedule->job(new NotifyDeadlineTask('hourly'))->hourly();
        $schedule->job(new NotifyDeadlineTask('daily'))->timezone('Asia/Ho_Chi_Minh')->dailyAt('07:00');
        $schedule->job(new NotifyBehindScheduleTask())->timezone('Asia/Ho_Chi_Minh')->dailyAt('07:00');
        $schedule->job(new NotifyBehindScheduleProject())->timezone('Asia/Ho_Chi_Minh')->dailyAt('07:00');
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
