<?php

namespace App\Services;

use App\Http\Resources\Permission\PermissionCollection;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

class PermissionService implements Contracts\PermissionServiceContract
{
    public function list (array $inputs = []) : JsonResponse
    {
        $permissions = Permission::all();
        return (new PermissionCollection($permissions))->response();
    }

    public function get (int|string $id, array $inputs = []) {}

    public function store (array $inputs) {}

    public function update (int|string $id, array $inputs) {}

    public function delete (int|string $id) {}
}
