<?php

namespace App\CommandBus\Commands\Role;

class CreateRoleCommand
{
    private string $name;
    private array $permissionIds;

    /**
     * @param string $name
     * @param array  $permissionIds
     */
    public function __construct (string $name, array $permissionIds)
    {
        $this->name = $name;
        $this->permissionIds = $permissionIds;
    }

    /**
     * @return string
     */
    public function getName () : string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getPermissionIds () : array
    {
        return $this->permissionIds;
    }
}