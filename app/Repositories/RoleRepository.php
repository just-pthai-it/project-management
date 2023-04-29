<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository extends Abstracts\ABaseRepository implements Contracts\RoleRepositoryContract
{
    /**
     * @inheritDoc
     */
    function model () : string
    {
        return Role::class;
    }
}
