<?php

namespace App\Listeners;

use App\Events\NotificationCreatedEvent;
use App\Events\TaskStatusUpdatedToReviewOrCompleteEvent;
use App\Models\Notification;
use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TaskStatusUpdatedToReviewOrCompleteListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct ()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param TaskStatusUpdatedToReviewOrCompleteEvent $event
     * @return void
     */
    public function handle (TaskStatusUpdatedToReviewOrCompleteEvent $event) : void
    {
        if ($event->task->status_id == TaskStatus::STATUS_REVIEW)
        {
            $this->__whenTaskUpdateToReviewStatus($event->task);
        }
        else if ($event->task->status_id == TaskStatus::STATUS_COMPLETE)
        {
            $this->__whenTaskUpdateToCompleteStatus($event->task);
        }
    }

    private function __whenTaskUpdateToReviewStatus (Task $task) : void
    {
        $notificationReceiverIds = [$task->user_id];
        $notificationContent     = __('notification.task_status_updated_to_review',
                                      ['task_name' => $task->name]);
        $notification            = $this->__storeNotification($task, ['content' => $notificationContent],
                                                              array_unique($notificationReceiverIds));
        $this->__broadcastNotification($notification, array_unique($notificationReceiverIds));
    }

    private function __whenTaskUpdateToCompleteStatus (Task $task) : void
    {
        if ($task->parent_id != null)
        {
            $notificationReceiverIds = array_merge($task->parent->users()->pluck('users.id')->toArray(),
                                                   [$task->parent->user_id]);
            $notificationContent     = __('notification.task_status_updated_to_complete',
                                          ['task_name' => $task->name]);
            $notification            = $this->__storeNotification($task, ['content' => $notificationContent],
                                                                  array_unique($notificationReceiverIds));
            $this->__broadcastNotification($notification, array_unique($notificationReceiverIds));
        }
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
}
