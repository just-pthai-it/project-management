<?php

namespace App\Services;

use App\Helpers\CusResponse;
use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class RoleService implements Contracts\RoleServiceContract
{
    private RoleRepositoryContract $roleRepository;

    public function __construct (RoleRepositoryContract $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function list (array $inputs = []) : JsonResponse
    {
        $roles = Role::all();
        return CusResponse::successful($roles);
    }

    public function get (int|string $id, array $inputs = []) {}

    public function store (array $inputs) : JsonResponse
    {
        $role = Role::query()->create(Arr::except($inputs, ['permission_ids']));
        if (isset($inputs['permission_ids']))
        {
            $role->permissions()->attach($inputs['permission_ids']);
        }
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
        return CusResponse::successful();
    }
}
