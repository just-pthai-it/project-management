<?php

namespace App\Services;

use App\Helpers\CusResponse;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

class PermissionService implements Contracts\PermissionServiceContract
{
    public function list (array $inputs = []) : JsonResponse
    {
        $permissions = Permission::all();
        return CusResponse::successful($permissions);
    }

    public function get (int|string $id, array $inputs = [])
    {

    }

    public function store (array $inputs)
    {

    }

    public function update (int|string $id, array $inputs)
    {

    }

    public function delete (int|string $id)
    {

    }
}
