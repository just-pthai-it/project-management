<?php

namespace App\CommandBus\Commands\Role;

use App\Models\Role;

class UpdateRoleCommand
{
    private Role $role;
    private array $input;

    /**
     * @param Role  $role
     * @param array $input
     */
    public function __construct (Role $role, array $input)
    {
        $this->role  = $role;
        $this->input = $input;
    }

    /**
     * @return Role
     */
    public function getRole () : Role
    {
        return $this->role;
    }

    /**
     * @return array
     */
    public function getInput () : array
    {
        return $this->input;
    }

}