<?php

namespace App\Listeners;

use App\Events\ActivityLogCreatedEvent;
use App\Events\ObjectResourceUpdatedEvent;
use App\Events\SystemObjectEvent;
use App\Events\UserCommentedEvent;
use App\Models\ActivityLog;
use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class SystemActivityEventSubscriber implements ShouldQueue
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

    public function handleSystemObjectEvent (SystemObjectEvent $event) : void
    {
        $descriptionProperties['causer_name'] = $event->causer->name;
        $descriptionProperties['object_name'] = $event->object->name;
        $descriptionProperties['object_type'] = __(Str::lower(class_basename(get_class($event->object))));

        if ($event->action == 'updated' && isset($event->dataChanges['status_id']))
        {
            $descriptionProperties['field'] = __('model_field.status_id');

            $oldObject                          = $event->object->replicate()->fill($event->oldData);
            $descriptionProperties['old_value'] = $oldObject->status->name;
            $descriptionProperties['value']     = $event->object->status->name;

            $data['description'] = __("activity_log.updated_with_field", $descriptionProperties);
        }
        else
        {
            $data['description'] = __("activity_log.{$event->action}", $descriptionProperties);
        }

        $data['user_id'] = $event->causer->id;
        $data['name']    = $event->action;
        $data['type_id'] = ActivityLog::OBJECT_CREATE_LOG_TYPE_ID;
        $this->__updateActivityLog($event->object, $data);

        if ($event->object instanceof Task && in_array($event->action, ['created', 'deleted']))
        {
            $this->__updateActivityLog($event->object->project, $data);
        }
    }

//    public function handleUserCommented (UserCommentedEvent $event) : void
//    {
//        if ($event->previousComment != null)
//        {
//            return;
//        }
//
//        $descriptionProperties[':object_name'] = $event->object->name;
//        $descriptionProperties[':user_name']   = $event->comment->user->name;
//        if ($event->object instanceof Task)
//        {
//            $descriptionProperties[':commentable'] = 'task';
//        }
//        else
//        {
//            $descriptionProperties[':commentable'] = 'project';
//        }
//        $data['description'] = Str::swap($descriptionProperties, ActivityLog::COMMENT_LOG_DESCRIPTION);
//        $data['type_id']     = ActivityLog::OBJECT_UPDATE_LOG_TYPE_ID;
//        $data['name']        = 'commented';
//        $data['user_id']     = $event->comment->user->id;
//        $data['comment_id']  = $event->comment->id;
//        $this->__updateActivityLog($event->object, $data);
//    }

    public function handleObjectResourceUpdated (ObjectResourceUpdatedEvent $event) : void
    {
        $descriptionProperties['causer_name'] = $event->causer->name;
        $descriptionProperties['object_name'] = $event->object->name;
        $descriptionProperties['object_type'] = __(Str::lower(class_basename(get_class($event->object))));

        $data['description'] = __("activity_log.{$event->action}", $descriptionProperties);
        $data['type_id']     = ActivityLog::OBJECT_UPDATE_LOG_TYPE_ID;
        $data['name']        = 'update';
        $data['user_id']     = $event->causer->id;
        $this->__updateActivityLog($event->object, $data);
    }

    private function __updateActivityLog (Model $object, array $data) : void
    {
        $activityLog = $object->activityLogs()->create($data);
        event(new ActivityLogCreatedEvent($activityLog));
    }

    public function subscribe ($events) : array
    {
        return [
            UserCommentedEvent::class         => 'handleUserCommented',
            ObjectResourceUpdatedEvent::class => 'handleObjectResourceUpdated',
            SystemObjectEvent::class          => 'handleSystemObjectEvent',
        ];
    }
}
