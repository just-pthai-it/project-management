<?php

namespace App\Services\Contracts;

use App\Models\ProjectStatus;

interface ProjectStatusServiceContract
{
    public function list (array $inputs = []);

    public function get (ProjectStatus $projectStatus);

    public function store (array $inputs);

    public function update (ProjectStatus $projectStatus, array $inputs);

    public function delete (ProjectStatus $projectStatus);
}
