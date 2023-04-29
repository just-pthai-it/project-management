<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends Abstracts\ABaseRepository implements Contracts\UserRepositoryContract
{
    /**
     * @inheritDoc
     */
    function model () : string
    {
        return User::class;
    }
}
