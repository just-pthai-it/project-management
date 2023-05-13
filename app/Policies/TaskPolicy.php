<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use mysql_xdevapi\Table;

class TaskPolicy
{
    use HandlesAuthorization;

    public function before (User $user, $ability) : ?bool
    {
        return $user->isRoot() ? true : null;
    }

    public function search (User $user) : bool
    {
        return $user->tokenCan('task:view-any');
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny (User $user) : Response|bool
    {
        return $user->tokenCan('task:view-any');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Task $task
     * @return Response|bool
     */
    public function view (User $user, Task $task) : Response|bool
    {
        return $user->tokenCan('task:view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create (User $user) : Response|bool
    {
        return $user->tokenCan('task:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Task $task
     * @return Response|bool
     */
    public function update (User $user, Task $task) : Response|bool
    {
        return ($user->tokenCan('task:update') &&
                $task->users()->where('users.id', '=', $user->id)->exists()) ||
               $user->tokenCan('project:create') ||
               $user->tokenCan('project:update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Task $task
     * @return Response|bool
     */
    public function delete (User $user, Task $task) : Response|bool
    {
        return ($user->tokenCan('task:delete') &&
                $task->users()->where('users.id', '=', $user->id)->exists()) ||
               $user->tokenCan('project:create') ||
               $user->tokenCan('project:update');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Task $task
     * @return Response|bool
     */
    public function restore (User $user, Task $task)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Task $task
     * @return Response|bool
     */
    public function forceDelete (User $user, Task $task)
    {
        //
    }

    public function attachFiles (User $user, Task $task) : bool
    {
        return $user->tokenCan('task:create');
    }

    public function detachFile (User $user, Task $task) : bool
    {
        return $user->tokenCan('task:create');
    }

    public function report (User $user, Task $task) : bool
    {
        return $user->tokenCan('task:report');
    }

    public function history (User $user, Task $task) : bool
    {
        return $user->tokenCan('task:view');
    }
}
