<?php

namespace App\Services\Contracts;

use App\Models\Role;

interface RoleServiceContract
{
    public function list (array $inputs = []);

    public function get (int|string $id, array $inputs = []);

    public function store (array $inputs);

    public function update (Role $role, array $inputs);

    public function delete (Role $role);
}
