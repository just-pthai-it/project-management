<?php

namespace App\CommandBus\Commands\Role;

class CreateRoleCommand
{
    private string $name;
    private array $permissionIds;

    /**
     * @param array $input
     */
    public function __construct (array $input)
    {
        $this->name          = $input['name'];
        $this->permissionIds = $input['permission_ids'];
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