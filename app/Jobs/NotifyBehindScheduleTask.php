<?php

namespace App\Jobs;

use App\Events\NotificationCreatedEvent;
use App\Mail\CloseToTheDeadline;
use App\Models\Notification;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyBehindScheduleTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle ()
    {
        Task::query()->where('status_id', '=', TaskStatus::STATUS_BEHIND_SCHEDULE)
            ->chunkById(50, function ($tasks)
            {
                foreach ($tasks as $task)
                {
                    $diffForHumans       = now('+7')->diffForHumans($task->created_at);
                    $notificationContent = __('notification.behind_schedule',
                                              ['object_type' => 'đầu việc',
                                               'object_name' => $task->name,
                                               'diff'        => $diffForHumans]);
                    $notificationContent = str_replace(' sau', '', $notificationContent);
                    $NotificationReceiverIds = array_merge($task->users()->pluck('users.id')->all(), [$task->user_id]);;
                    $notification = $this->__storeNotification($task, ['content' => $notificationContent],
                                                               array_unique($NotificationReceiverIds));
                    $this->__broadcastNotification($notification, array_unique($NotificationReceiverIds));
                    $this->__mailToTheProjectOwner($notification, $task->user);
                }
            });
    }


    private function __storeNotification (Task $task, array $data, array $userIds) : Notification
    {
        $notification = $task->notification()->create($data);
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
