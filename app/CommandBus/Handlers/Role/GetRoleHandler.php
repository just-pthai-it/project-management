<?php

namespace App\CommandBus\Handlers\Role;

use App\CommandBus\Commands\Role\GetRoleCommand;
use App\CommandBus\Handler\Handler;
use App\Models\Role;

class GetRoleHandler implements Handler
{
    /**
     * @param GetRoleCommand $command
     * @return Role
     */
    public function handle ($command) : Role
    {
        $role = $command->getRole();
        $role->load('permissions');
        return $role;
    }
}