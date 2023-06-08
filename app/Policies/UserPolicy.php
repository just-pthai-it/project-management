<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public function before (User $loggedUser, $ability) : ?bool
    {
        return $loggedUser->tokenCan('*') ? true : null;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param User $loggedUser
     * @return Response|bool
     */
    public function viewAny (User $loggedUser) : Response|bool
    {
        return $loggedUser->tokenCan('user:view-any');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $loggedUser
     * @return Response|bool
     */
    public function view (User $loggedUser) : Response|bool
    {
        return $loggedUser->tokenCan('user:view');

    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $loggedUser
     * @return bool
     */
    public function create (User $loggedUser) : bool
    {
        return $loggedUser->tokenCan('user:create');

    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $loggedUser
     * @param User $user
     * @return Response|bool
     */
    public function update (User $loggedUser, User $user) : Response|bool
    {
        return $loggedUser->tokenCan('user:update') && !$user->isRoot();

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $loggedUser
     * @param User $user
     * @return bool
     */
    public function delete (User $loggedUser, User $user) : bool
    {
        return $loggedUser->tokenCan('user:delete') && !$user->isRoot();

    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $loggedUser
     * @return void
     */
    public function restore (User $loggedUser)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $loggedUser
     * @param User $user
     * @return Response|bool
     */
    public function forceDelete (User $loggedUser, User $user) : Response|bool
    {
        return $loggedUser->tokenCan('user:delete') && !$user->isRoot();
    }
}
