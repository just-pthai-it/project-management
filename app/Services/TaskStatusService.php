<?php

namespace App\Services;

use App\Helpers\CusResponse;
use App\Models\TaskStatus;
use Illuminate\Http\JsonResponse;

class TaskStatusService implements Contracts\TaskStatusServiceContract
{
    public function list (array $inputs = []) : JsonResponse
    {
        $taskStatuses = TaskStatus::all();
        return CusResponse::successful($taskStatuses);
    }

    public function get (TaskStatus $taskStatus)
    {

    }

    public function store (array $inputs)
    {

    }

    public function update (TaskStatus $taskStatus, array $inputs)
    {

    }

    public function delete (TaskStatus $taskStatus)
    {

    }
}
