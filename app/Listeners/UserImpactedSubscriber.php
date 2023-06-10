<?php

namespace App\Listeners;

use App\Events\NotificationCreatedEvent;
use App\Events\ObjectResourceUpdatedEvent;
use App\Events\UserAssignedEvent;
use App\Events\UserCommentedEvent;
use App\Mail\UserAssigned;
use App\Models\Notification;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserImpactedSubscriber implements ShouldQueue
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

    public function handleObjectResourceUpdatedEvent (ObjectResourceUpdatedEvent $event) : void
    {
        if (in_array($event->action, ['detached_file', 'deleted_report']))
        {
            return;
        }

        $content = __("notification.{$event->action}",
                      ['causer_name' => $event->causer->name,
                       'object_type' => __(Str::lower(class_basename(get_class($event->object)))),
                       'object_name' => $event->object->name]);

        if ($event->action == 'attached_file')
        {
            $receiverIds = array_diff($event->object->users()->pluck('users.id')->all(), [$event->causer->id]);
        }
        else
        {
            $receiverIds = [$event->object->user->id];
        }

        $notification = $this->__storeNotification($event->object, ['content' => $content], $receiverIds);
        $this->__broadcastNotification($notification, $receiverIds);
    }

    public function handleUserCommentedEvent (UserCommentedEvent $event) : void
    {
        if ($event->previousComment == null ||
            $event->comment->user_id == $event->previousComment->user_id)
        {
            return;
        }

        $content = Str::swap([':user_name'   => $event->comment->user->name,
                              ':object_type' => __(Str::lower(class_basename(get_class($event->object)))),
                              ':object_name' => $event->object->name],
                             Notification::USER_COMMENTED_NOTIFICATION_CONTENT);

        $notification = $this->__storeNotification($event->comment, ['content' => $content],
                                                   [$event->previousComment->user_id]);
        $this->__broadcastNotification($notification, [$event->previousComment->user_id]);
    }

    public function handleUserAssignedEvent (UserAssignedEvent $event) : void
    {
        $content = __('notification.user_assigned',
                      ['causer_name' => $event->causer->name,
                       'object_type' => __(Str::lower(class_basename(get_class($event->object)))),
                       'object_name' => $event->object->name]);

        $notification = $this->__storeNotification($event->object, ['content' => $content], $event->userIds);
        $this->__broadcastNotification($notification, $event->userIds);
        $this->__mailToAssignee($notification, $event->userIds);
    }

    private function __storeNotification (Model $model, array $data, array $userIds) : Notification
    {
        $notification = $model->notification()->create($data);
        $notification->users()->attach($userIds);
        return $notification;
    }

    private function __broadcastNotification (Notification $notification, array $userIds) : void
    {
        event(new NotificationCreatedEvent($notification, $userIds));
    }

    private function __mailToAssignee (Notification $notification, array $userIds) : void
    {
        $users = User::query()->findMany($userIds, ['name', 'email']);
        Mail::to($users)->send(new UserAssigned($notification));
    }

    public function subscribe ($events) : array
    {
        return [
            UserCommentedEvent::class         => 'handleUserCommentedEvent',
            UserAssignedEvent::class          => 'handleUserAssignedEvent',
            ObjectResourceUpdatedEvent::class => 'handleObjectResourceUpdatedEvent',
        ];
    }
}
