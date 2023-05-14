<?php

namespace App\Services\Contracts;

use App\Models\Project;
use App\Models\Task;

interface ProjectServiceContract
{
    public function search (array $inputs = []);

    public function list (array $inputs = []);

    public function get (Project $project, array $inputs = []);

    public function store (array $inputs);

    public function update (Project $project, array $inputs);

    public function delete (Project $project);

    public function searchTasks (Project $project, array $inputs = []);

    public function listTasks (Project $project, array $inputs = []);

    public function getTask (Project $project, Task $task);

    public function storeTask (Project $project, array $inputs);

    public function updateTask (Project $project, Task $task, array $inputs);

    public function deleteTask (Project $project, Task $task);

    public function listUsers (Project $project, array $inputs = []);

    public function history (Project $project);
}
