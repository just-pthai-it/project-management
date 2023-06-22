<?php

namespace App\CommandBus\Handlers\Role;

use App\CommandBus\Commands\Role\UpdateRoleCommand;
use App\CommandBus\Handler\Handler;
use App\Models\Role;

class UpdateRoleHandler implements Handler
{
    /**
     * @param UpdateRoleCommand $command
     * @return void
     */
    public function handle ($command) : void
    {
        $role  = $command->getRole();
        $role->update(['name' => $command->getName()]);
        if ($command->getPermissionIds() != null)
        {
            $this->__syncPermissions($role, $command->getPermissionIds());
        }
    }

    private function __syncPermissions (Role $role, array $permissionIds) : void
    {
        $role->permissions()->sync($permissionIds);
    }
}