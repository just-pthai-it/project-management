<?php

namespace App\CommandBus\Handlers\Role;

use App\CommandBus\Commands\Role\UpdateRoleCommand;
use App\CommandBus\Handler\Handler;
use App\Models\Role;
use Illuminate\Support\Arr;

class UpdateRoleHandler implements Handler
{
    /**
     * @param UpdateRoleCommand $command
     * @return void
     */
    public function handle ($command) : void
    {
        $role  = $command->getRole();
        $input = $command->getInput();
        $role->update(Arr::except($input, ['permission_ids']));
        if (isset($input['permission_ids']))
        {
            $this->__syncPermissions($role, $input['permission_ids']);
        }
    }

    private function __syncPermissions (Role $role, array $permissionIds) : void
    {
        $role->permissions()->sync($permissionIds);
    }
}