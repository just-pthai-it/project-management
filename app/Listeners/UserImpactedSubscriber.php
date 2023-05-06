<?php

namespace App\Listeners;

use App\Events\NotificationCreated;
use App\Events\UserAssigned;
use App\Events\UserCommented;
use App\Models\Notification;
use App\Models\Project;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class UserImpactedSubscriber
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

    public function handleUserCommented (UserCommented $event) : void
    {
        if ($event->previousComment == null ||
            $event->comment->user_id == $event->previousComment->user_id)
        {
            return;
        }

        $content = Str::swap([':user_name'   => $event->comment->user->name,
                              ':object'      => 'task',
                              ':object_name' => $event->object->name],
                             Notification::USER_COMMENTED_NOTIFICATION_CONTENT);

        $this->__storeNotification($event->comment, ['content' => $content],
                                   [$event->previousComment->user_id]);
    }

    public function handleUserAssigned (UserAssigned $event) : void
    {
        if (empty($event->userIds))
        {
            return;
        }

        $content = Str::swap([':user_name'   => $event->object->user->name,
                              ':object'      => $event->object instanceof Project ? 'project' : 'task',
                              ':object_name' => $event->object->name],
                             Notification::USER_ASSIGNED_NOTIFICATION_CONTENT);

        $this->__storeNotification($event->object, ['content' => $content], $event->userIds);
    }

    private function __storeNotification (Model $model, array $data, array $userIds) : void
    {
        $notification = $model->notification()->create($data);
        $notification->users()->attach($userIds);
        event(new NotificationCreated($notification, $userIds));
    }

    public function subscribe ($events) : array
    {
        return [
            UserCommented::class => 'handleUserCommented',
            UserAssigned::class  => 'handleUserAssigned',
        ];
    }
}
