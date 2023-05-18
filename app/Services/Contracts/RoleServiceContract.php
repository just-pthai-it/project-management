<?php

namespace App\Services\Contracts;

use App\Models\Role;

interface RoleServiceContract
{
    public function list (array $inputs = []);

    public function get (Role $role);

    public function store (array $inputs);

    public function update (Role $role, array $inputs);

    public function delete (Role $role);
}
