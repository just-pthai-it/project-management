<?php

namespace App\Services;

use App\Helpers\CusResponse;
use App\Http\Resources\Role\RoleResource;
use App\Models\Role;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class RoleService implements Contracts\RoleServiceContract
{

    public function list (array $inputs = []) : JsonResponse
    {
        $roles = Role::query()->when(!auth()->user()->isRoot(), function (Builder $query)
        {
            $query->where('name', '!=', Role::ROLE_ROOT_NAME);
        })->get();
        return RoleResource::collection($roles)->response();
    }

    public function get (Role $role) : JsonResponse
    {
        $role->load('permissions');
        return (new RoleResource($role))->response();
    }

    public function store (array $inputs) : JsonResponse
    {
        $role = Role::query()->create(Arr::except($inputs, ['permission_ids']));
        $role->permissions()->attach($inputs['permission_ids']);
        return CusResponse::createSuccessful($role);
    }

    public function update (Role $role, array $inputs) : JsonResponse
    {
        $role->update(Arr::except($inputs, ['permission_ids']));
        if (isset($inputs['permission_ids']))
        {
            $role->permissions()->sync($inputs['permission_ids']);
        }
        return CusResponse::successful();
    }

    public function delete (Role $role) : JsonResponse
    {
        $role->users()->detach();
        $role->permissions()->detach();
        $role->delete();
        return CusResponse::successfulWithNoData();
    }
}
