<?php

namespace App\CommandBus\Commands\Role;

use App\Models\Role;

class DeleteRoleCommand
{
    private Role $role;

    /**
     * @param Role $role
     */
    public function __construct (Role $role)
    {
        $this->role = $role;
    }

    /**
     * @return Role
     */
    public function getRole () : Role
    {
        return $this->role;
    }
}