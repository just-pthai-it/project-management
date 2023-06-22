<?php

namespace App\CommandBus\Actions;

use App\Events\UsersAssignedEvent;
use App\Models\Project;
use App\Models\Task;

class AssignUsers
{
    public function __invoke (Project|Task $object, array $userIds) : void
    {
        $currentUser = auth()->user();

        $currentAssigneeIds = $this->__getCurrentAssigneeIds($object);
        $newAssigneeIds     = array_diff($userIds, $currentAssigneeIds);
        $oldAssigneeIds     = array_diff($currentAssigneeIds, $userIds);

        $object->users()->attach($newAssigneeIds);
        $object->users()->detach($oldAssigneeIds);

        if ($object instanceof Project &&
            !$object->wasRecentlyCreated &&
            !empty($oldAssigneeIds))
        {
            $this->__unassignUserFromTasksOfProject($object, $oldAssigneeIds);
        }

        if (!empty($newAssigneeIds))
        {
            event(new UsersAssignedEvent($object, $currentUser, array_diff($newAssigneeIds, [$currentUser->id])));
        }
    }

    private function __getCurrentAssigneeIds (Project|Task $object) : array
    {
        if ($object->wasRecentlyCreated)
        {
            return [];
        }

        return $object->users()->pluck('users.id')->toArray();
    }

    private function __unassignUserFromTasksOfProject (Project $project, array $oldAssigneeIds) : void
    {
        foreach ($project->tasks as $task)
        {
            $task->users()->detach($oldAssigneeIds);
        }
    }
}