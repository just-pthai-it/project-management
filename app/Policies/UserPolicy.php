<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
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
        return $user->can('user:view-any');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @return Response|bool
     */
    public function view (User $user) : Response|bool
    {
        return $user->can('user:view');

    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create (User $user) : bool
    {
        return $user->can('user:create');

    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return Response|bool
     */
    public function update (User $user) : Response|bool
    {
        return $user->can('user:update');

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete (User $user) : bool
    {
        return $user->can('user:delete');

    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @return void
     */
    public function restore (User $user)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @return Response|bool
     */
    public function forceDelete (User $user) : Response|bool
    {
        return $user->can('user:delete');
    }
}
