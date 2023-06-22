<?php

namespace App\CommandBus\Handlers\Role;

use App\CommandBus\Commands\Role\CreateRoleCommand;
use App\CommandBus\Handler\Handler;
use App\Models\Role;

class CreateRoleHandler implements Handler
{
    /**
     * @param CreateRoleCommand $command
     * @return Role
     */
    public function handle ($command) : Role
    {
        $role = Role::query()->create(['name' => $command->getName()]);
        $this->__attachPermissions($role, $command->getPermissionIds());
        return $role;
    }

    private function __attachPermissions (Role $role, array $permissionIds) : void
    {
        $role->permissions()->attach($permissionIds);
    }
}