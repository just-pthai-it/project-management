<?php

namespace App\Services\Contracts;

interface ProjectServiceContract
{
    public function list (array $inputs = []);

    public function get (int $id, array $inputs = []);

    public function store (array $inputs);

    public function update (int $id, array $inputs);

    public function delete (int $id);
}
