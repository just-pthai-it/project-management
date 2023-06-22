<?php

namespace App\CommandBus\Commands\Role;

use App\Models\Role;

class UpdateRoleCommand
{
    private Role $role;
    private string|null $name;
    private array|null $permissionIds;

    /**
     * @param Role  $role
     * @param array $input
     */
    public function __construct (Role $role, array $input)
    {
        $this->role          = $role;
        $this->name          = $input['name'] ?? null;
        $this->permissionIds = $input['permission_ids'] ?? null;
    }

    /**
     * @return Role
     */
    public function getRole () : Role
    {
        return $this->role;
    }

    /**
     * @return string|null
     */
    public function getName () : ?string
    {
        return $this->name;
    }

    /**
     * @return array|null
     */
    public function getPermissionIds () : ?array
    {
        return $this->permissionIds;
    }
}