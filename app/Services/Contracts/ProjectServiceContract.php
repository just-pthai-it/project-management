<?php

namespace App\Services\Contracts;

use App\Models\Project;

interface ProjectServiceContract
{
    public function list (array $inputs = []);

    public function get (Project $project, array $inputs = []);

    public function store (array $inputs);

    public function update (Project $project, array $inputs);

    public function delete (Project $project);
}
