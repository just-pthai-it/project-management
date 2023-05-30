<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\ProjectStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateBehindScheduleProjectStatus implements ShouldQueue
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
        Project::query()
               ->whereNotIn('status_id', [ProjectStatus::STATUS_BEHIND_SCHEDULE, ProjectStatus::STATUS_COMPLETE])
               ->where('ends_at', '>', now('+7'))
               ->update(['status_id' => ProjectStatus::STATUS_BEHIND_SCHEDULE]);

    }
}
