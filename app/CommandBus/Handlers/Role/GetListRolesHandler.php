<?php

namespace App\CommandBus\Handlers\Role;

use App\CommandBus\Commands\Role\GetListRolesCommand;
use App\CommandBus\Handler\Handler;
use App\Models\Role;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class GetListRolesHandler implements Handler
{
    /**
     * @param GetListRolesCommand $command
     * @return Collection
     */
    public function handle ($command) : Collection
    {
//        $input = $command->getInput();RoleResource::collection($roles)->response()
        $roles = Role::query()->when(!auth()->user()->tokenCan('*'), function (Builder $query)
        {
            $query->where('name', '!=', Role::ROLE_ROOT_NAME);
        })->get();
        return $roles;
    }
}