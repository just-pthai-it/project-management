<?php

namespace App\Repositories;

use App\Models\EmployeeLevel;

class EmployeeLevelRepository extends Abstracts\ABaseRepository implements Contracts\EmployeeLevelRepositoryContract
{
    /**
     * @inheritDoc
     */
    function model () : string
    {
        return EmployeeLevel::class;
    }
}
