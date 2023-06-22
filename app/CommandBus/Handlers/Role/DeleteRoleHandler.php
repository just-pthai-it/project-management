<?php

namespace App\CommandBus\Handlers\Role;

use App\CommandBus\Commands\Role\DeleteRoleCommand;
use App\CommandBus\Handler\Handler;

class DeleteRoleHandler implements Handler
{
    /**
     * @param DeleteRoleCommand $command
     * @return void
     */
    public function handle ($command) : void
    {
        $role = $command->getRole();
        $role->users()->detach();
        $role->permissions()->detach();
        $role->delete();
    }
}