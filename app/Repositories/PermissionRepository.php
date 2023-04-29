<?php

namespace App\Repositories;

use App\Models\Permission;

class PermissionRepository extends Abstracts\ABaseRepository implements Contracts\PermissionRepositoryContract
{
    /**
     * @inheritDoc
     */
    function model () : string
    {
        return Permission::class;
    }
}
