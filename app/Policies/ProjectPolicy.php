<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function before (User $user, $ability) : ?bool
    {
        return $user->isRoot() ? true : null;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny (User $user) : Response|bool
    {
        return $user->tokenCan('project:view-any');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User    $user
     * @param Project $project
     * @return Response|bool
     */
    public function view (User $user, Project $project) : Response|bool
    {
        return $user->tokenCan('project:view') &&
               $project->users()->where('users.id', '=', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create (User $user) : Response|bool
    {
        return $user->tokenCan('project:create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User    $user
     * @param Project $project
     * @return Response|bool
     */
    public function update (User $user, Project $project) : Response|bool
    {
        return $user->tokenCan('project:update') &&
               $project->users()->where('users.id', '=', $user->id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User    $user
     * @param Project $project
     * @return Response|bool
     */
    public function delete (User $user, Project $project) : Response|bool
    {
        return $user->tokenCan('project:delete') &&
               $project->users()->where('users.id', '=', $user->id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User    $user
     * @param Project $project
     * @return Response|bool
     */
    public function restore (User $user, Project $project)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User    $user
     * @param Project $project
     * @return Response|bool
     */
    public function forceDelete (User $user, Project $project)
    {
        //
    }
}
