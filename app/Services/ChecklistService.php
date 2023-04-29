<?php

namespace App\Services;

use App\Repositories\Contracts\ChecklistRepositoryContract;

class ChecklistService implements Contracts\ChecklistServiceContract
{
    private ChecklistRepositoryContract $checklistRepository;

    public function __construct (ChecklistRepositoryContract $checklistRepository)
    {
        $this->checklistRepository = $checklistRepository;
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
