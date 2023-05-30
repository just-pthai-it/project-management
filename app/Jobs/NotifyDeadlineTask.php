<?php

namespace App\Jobs;

use App\Events\NotificationCreatedEvent;
use App\Mail\CloseToTheDeadline;
use App\Models\Notification;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NotifyDeadlineTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $scheduleType;

    /**
     * Create a new job instance.
     *
     * @param string $scheduleType
     * @return void
     */
    public function __construct (string $scheduleType)
    {
        //
        $this->scheduleType = $scheduleType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle () : void
    {
        $tomorrow          = Carbon::now('+7')->addDay();
        $nexTwelveHours    = Carbon::now('+7')->addHours(12);
        $nextThirteenHours = Carbon::now('+7')->addHours(13);
        Task::query()
            ->whereNotIn('status_id', [TaskStatus::STATUS_BEHIND_SCHEDULE, ProjectStatus::STATUS_COMPLETE])
            ->when($this->scheduleType == 'hourly',
                fn (Builder $query) => $query->whereBetween('ends_at', [$nexTwelveHours, $nextThirteenHours]))
            ->when($this->scheduleType == 'daily',
                fn (Builder $query) => $query->where('ends_at', '=', $tomorrow->toDateString()))
            ->chunkById(50, function ($tasks)
            {
                foreach ($tasks as $task)
                {
                    $task->load('users:id,name,email');

                    $content            = $this->__generateContentForNotification($task);
                    $notification       = $this->__storeNotification($task, ['content' => $content],
                                                                     [$task->user_id]);
                    $broadcastReceivers = array_merge($task->users()->pluck('users.id')->all(), [$task->user_id]);
                    $this->__broadcastNotification($notification, $broadcastReceivers);
                    $this->__mailToTheProjectOwner($notification, $task->user);
                }
            });
    }

    private function __generateContentForNotification (Task $task) : string
    {
        $data[':object']       = Str::ucfirst(__(Str::lower(class_basename(get_class($task)))));
        $data[':object_name'] = $task->name;
        $data[':time']         = $this->scheduleType == 'daily' ? 1 : 12;
        $data[':time_unit']    = __($this->scheduleType == 'daily' ? 'day' : 'hour');
        return Str::swap($data, Notification::NOTIFY_DEADLINE_NOTIFICATION_CONTENT);
    }

    private function __storeNotification (Task $project, array $data, array $userIds) : Notification
    {
        $notification = $project->notification()->create($data);
        $notification->users()->attach($userIds);
        return $notification;
    }

    private function __broadcastNotification (Notification $notification, array $userIds) : void
    {
        event(new NotificationCreatedEvent($notification, $userIds));
    }

    private function __mailToTheProjectOwner (Notification $notification, User $owner) : void
    {
        Mail::to($owner)->send(new CloseToTheDeadline($notification));
    }
}
