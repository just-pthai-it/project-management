<?php

namespace App\Listeners;

use App\Events\ObjectResourceUpdated;
use App\Events\SystemObjectAffected;
use App\Events\UserCommented;
use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class SystemEventSubscriber
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

    public function handleSystemObjectAffected (SystemObjectAffected $event) : void
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
    }

    public function handleUserCommented (UserCommented $event) : void
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
        $data['name']        = 'update';
        $data['user_id']     = $event->comment->user->id;
        $data['comment_id']  = $event->comment->id;
        $this->__updateActivityLog($event->object, $data);
    }

    public function handleObjectResourceUpdated (ObjectResourceUpdated $event) : void
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
        $object->activityLogs()->create($data);
    }

    public function subscribe ($events) : array
    {
        return [
            UserCommented::class         => 'handleUserCommented',
            ObjectResourceUpdated::class => 'handleObjectResourceUpdated',
            SystemObjectAffected::class  => 'handleSystemObjectAffected',
        ];
    }
}
