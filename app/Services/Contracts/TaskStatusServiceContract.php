<?php

namespace App\Services\Contracts;

use App\Models\TaskStatus;

interface TaskStatusServiceContract
{
    public function list (array $inputs = []);

    public function get (TaskStatus $taskStatus);

    public function store (array $inputs);

    public function update (TaskStatus $taskStatus, array $inputs);

    public function delete (TaskStatus $taskStatus);
}
