<?php

namespace App\Services;

use App\Repositories\Contracts\EmployeeLevelRepositoryContract;

class EmployeeLevelService implements Contracts\EmployeeLevelServiceContract
{
    private EmployeeLevelRepositoryContract $employeeLevelRepository;

    public function __construct (EmployeeLevelRepositoryContract $employeeLevelRepository)
    {
        $this->employeeLevelRepository = $employeeLevelRepository;
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
