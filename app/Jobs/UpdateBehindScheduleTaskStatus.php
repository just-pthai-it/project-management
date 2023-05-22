<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateBehindScheduleTaskStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct ()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle () : void
    {
        Task::query()->where('ends_at', '>', now('+7'))
            ->update(['status_id' => TaskStatus::STATUS_BEHIND_SCHEDULE]);
    }
}
