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
        return $user->tokenCan('*') ? true : null;
    }

    public function search (User $user) : bool
    {
        return $user->tokenCan('project:view-any');
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
        return $user->tokenCan('statistical:project') ||
               ($user->tokenCan('project:view') &&
                ($project->user_id == $user->id ||
                 $project->users()->where('users.id', '=', $user->id)->exists()));
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
               ($project->user_id == $user->id ||
                $project->users()->where('users.id', '=', $user->id)->exists());
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
        return $user->tokenCan('project:delete') && $project->user_id == $user->id;
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

    public function history (User $user, Project $project) : bool
    {
        return $user->tokenCan('project:view') &&
               ($user->tokenCan('statistical:project') ||
                $project->user_id == $user->id ||
                $project->users()->where('users.id', '=', $user->id)->exists());
    }
}
