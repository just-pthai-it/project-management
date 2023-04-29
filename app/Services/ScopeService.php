<?php

namespace App\Services;

use App\Repositories\Contracts\ScopeRepositoryContract;

class ScopeService implements Contracts\ScopeServiceContract
{
    private ScopeRepositoryContract $scopeRepository;

    public function __construct (ScopeRepositoryContract $scopeRepository)
    {
        $this->scopeRepository = $scopeRepository;
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
