<?php

namespace App\Listeners;

use App\Events\ObjectCreated;
use App\Events\ObjectResourceUpdated;
use App\Events\ObjectUpdated;
use App\Events\UserCommented;
use App\Models\ActivityLog;
use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class EventSubscriber
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

    public function handleObjectCreated (ObjectCreated $event) : void
    {
        $descriptionProperties[':object_name'] = $event->object->name;
        $descriptionProperties[':user_name']   = $event->user->name;

        if ($event->object instanceof Task)
        {
            $descriptionProperties[':object'] = 'task';
        }
        else
        {
            $descriptionProperties[':object'] = 'project';
        }

        $data['description'] = Str::swap($descriptionProperties, ActivityLog::OBJECT_CREATE_LOG_DESCRIPTION);
        $data['user_id']     = $event->user->id;
        $data['name']        = 'create';
        $data['type_id']     = ActivityLog::OBJECT_CREATE_LOG_TYPE_ID;

        $this->__updateActivityLog($event->object, $data);
    }

    public function handleObjectUpdated (ObjectUpdated $event) : void
    {
        $oldObject                             = $event->object->replicate()->fill($event->oldData);
        $descriptionProperties[':object_name'] = $event->object->name;
        $descriptionProperties[':user_name']   = $event->user->name;
        if ($event->object instanceof Task)
        {
            $descriptionProperties[':object'] = 'task';
        }
        else
        {
            $descriptionProperties[':object'] = 'project';
        }

        $changes = array_diff($event->object->getOriginal(), $oldObject->getOriginal());
        if (isset($changes['status_id']))
        {
            $descriptionProperties[':attribute'] = 'status';
            $descriptionProperties[':old_value'] = $oldObject->status->name;
            $descriptionProperties[':new_value'] = $event->object->status->name;

            $data['description'] = Str::swap($descriptionProperties,
                                             ActivityLog::OBJECT_UPDATE_ATTRIBUTE_LOG_DESCRIPTION);
        }
        else
        {
            $data['description'] = Str::swap($descriptionProperties, ActivityLog::OBJECT_UPDATE_LOG_DESCRIPTION);
        }

        $data['user_id'] = $event->user->id;
        $data['name']    = 'update';
        $data['type_id'] = ActivityLog::OBJECT_UPDATE_LOG_TYPE_ID;
        $this->__updateActivityLog($event->object, $data);
    }

    public function handleUserCommented (UserCommented $event) : void
    {
        $descriptionProperties[':object_name'] = $event->object->name;
        $descriptionProperties[':user_name']   = $event->user->name;
        if ($event->object instanceof Task)
        {
            $descriptionProperties[':object'] = 'task';
        }
        else
        {
            $descriptionProperties[':object'] = 'project';
        }
        $data['description'] = Str::swap($descriptionProperties, ActivityLog::COMMENT_LOG_DESCRIPTION);
        $data['type_id']     = ActivityLog::OBJECT_UPDATE_LOG_TYPE_ID;
        $data['name']        = 'update';
        $data['user_id'] = $event->user->id;
        $data['comment_id']  = $event->commentId;
        $this->__updateActivityLog($event->object, $data);
    }

    public function handleObjectResourceUpdated (ObjectResourceUpdated $event) : void
    {
        $descriptionProperties[':object_name'] = $event->object->name;
        $descriptionProperties[':user_name']   = $event->user->name;
        if ($event->object instanceof Task)
        {
            $descriptionProperties[':object'] = 'task';
        }
        else
        {
            $descriptionProperties[':object'] = 'project';
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
            ObjectCreated::class         => 'handleObjectCreated',
            ObjectUpdated::class         => 'handleObjectUpdated',
            UserCommented::class         => 'handleUserCommented',
            ObjectResourceUpdated::class => 'handleObjectResourceUpdated',
        ];
    }
}
