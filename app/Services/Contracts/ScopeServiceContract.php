<?php

namespace App\Services\Contracts;

interface ScopeServiceContract
{
    public function list (array $inputs = []);

    public function get (int|string $id, array $inputs = []);

    public function store (array $inputs);

    public function update (int|string $id, array $inputs);

    public function delete (int|string $id);
}
