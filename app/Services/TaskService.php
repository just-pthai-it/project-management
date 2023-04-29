<?php

namespace App\Services;

use App\Repositories\Contracts\TaskRepositoryContract;

class TaskService implements Contracts\TaskServiceContract
{
    private TaskRepositoryContract $taskRepository;

    public function __construct (TaskRepositoryContract $taskRepository)
    {
        $this->taskRepository = $taskRepository;
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
