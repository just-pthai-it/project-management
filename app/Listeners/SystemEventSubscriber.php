<?php

namespace App\Listeners;

use App\Events\ActivityLogCreatedEvent;
use App\Events\ObjectResourceUpdatedEvent;
use App\Events\SystemObjectAffectedEvent;
use App\Events\UserCommentedEvent;
use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class SystemEventSubscriber implements ShouldQueue
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

    public function handleSystemObjectAffected (SystemObjectAffectedEvent $event) : void
    {
        $descriptionProperties[':object_name'] = $event->object->name;
        $descriptionProperties[':user_name']   = $event->causer->name;
        $descriptionProperties[':action']      = $event->action;
        $descriptionProperties[':object_type'] = Str::lower(class_basename(get_class($event->object)));
        $descriptionProperties[':extra']       = '';

        if (!empty($event->oldData) && $event->action == 'updated')
        {
            $oldObject         = $event->object->replicate()->fill($event->oldData);
            $changedAttributes = array_diff($event->object->getOriginal(), $event->oldData);

            if (isset($changedAttributes['status_id']))
            {
                $extraDescription = "status from {$oldObject->status->name} to {$event->object->status->name} in";;
                $descriptionProperties[':extra'] = $extraDescription;
            }
        }

        $data['description'] = Str::swap($descriptionProperties, ActivityLog::SYSTEM_OBJECT_AFFECTED_LOG_DESCRIPTION);
        $data['description'] = Str::squish($data['description']);
        $data['user_id']     = $event->causer->id;
        $data['name']        = $event->action;
        $data['type_id']     = ActivityLog::OBJECT_CREATE_LOG_TYPE_ID;
        $this->__updateActivityLog($event->object, $data);
        if ($event->object instanceof Task && in_array($event->action, ['created', 'deleted']))
        {
            $this->__updateActivityLog($event->object->project, $data);
        }
    }

    public function handleUserCommented (UserCommentedEvent $event) : void
    {
        if ($event->previousComment != null)
        {
            return;
        }

        $descriptionProperties[':object_name'] = $event->object->name;
        $descriptionProperties[':user_name']   = $event->comment->user->name;
        if ($event->object instanceof Task)
        {
            $descriptionProperties[':commentable'] = 'task';
        }
        else
        {
            $descriptionProperties[':commentable'] = 'project';
        }
        $data['description'] = Str::swap($descriptionProperties, ActivityLog::COMMENT_LOG_DESCRIPTION);
        $data['type_id']     = ActivityLog::OBJECT_UPDATE_LOG_TYPE_ID;
        $data['name']        = 'commented';
        $data['user_id']     = $event->comment->user->id;
        $data['comment_id']  = $event->comment->id;
        $this->__updateActivityLog($event->object, $data);
    }

    public function handleObjectResourceUpdated (ObjectResourceUpdatedEvent $event) : void
    {
        $descriptionProperties[':object_name'] = $event->object->name;
        $descriptionProperties[':user_name']   = $event->user->name;
        if ($event->object instanceof Task)
        {
            $descriptionProperties[':commentable'] = 'task';
        }
        else
        {
            $descriptionProperties[':commentable'] = 'project';
        }

        $descriptionProperties[':action']      = $event->action;
        $descriptionProperties[':resource']    = $event->resource;
        $descriptionProperties[':preposition'] = $event->preposition;

        $data['description'] = Str::swap($descriptionProperties, ActivityLog::OBJECT_RESOURCE_UPDATE_LOG_DESCRIPTION);
        $data['type_id']     = ActivityLog::OBJECT_UPDATE_LOG_TYPE_ID;
        $data['name']        = 'update';
        $data['user_id']     = $event->user->id;
        $this->__updateActivityLog($event->object, $data);
    }

    private function __updateActivityLog (Model $object, array $data) : void
    {
      $activityLog =  $object->activityLogs()->create($data);
        event(new ActivityLogCreatedEvent($activityLog));
    }

    public function subscribe ($events) : array
    {
        return [
            UserCommentedEvent::class         => 'handleUserCommented',
            ObjectResourceUpdatedEvent::class => 'handleObjectResourceUpdated',
            SystemObjectAffectedEvent::class  => 'handleSystemObjectAffected',
        ];
    }
}
