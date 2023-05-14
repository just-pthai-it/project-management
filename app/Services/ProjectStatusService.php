<?php

namespace App\Services;

use App\Helpers\CusResponse;
use App\Models\ProjectStatus;
use Illuminate\Http\JsonResponse;

class ProjectStatusService implements Contracts\ProjectStatusServiceContract
{
    public function list (array $inputs = []) : JsonResponse
    {
        $projectStatuses = ProjectStatus::all();
        return CusResponse::successful($projectStatuses);
    }

    public function get (ProjectStatus $projectStatus)
    {

    }

    public function store (array $inputs)
    {

    }

    public function update (ProjectStatus $projectStatus, array $inputs)
    {

    }

    public function delete (ProjectStatus $projectStatus)
    {

    }
}
