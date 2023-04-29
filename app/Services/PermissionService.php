<?php

namespace App\Services;

use App\Repositories\Contracts\PermissionRepositoryContract;

class PermissionService implements Contracts\PermissionServiceContract
{
    private PermissionRepositoryContract $permissionRepository;

    public function __construct (PermissionRepositoryContract $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function list (array $inputs = [])
    {

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
