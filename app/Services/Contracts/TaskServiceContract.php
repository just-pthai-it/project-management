<?php

namespace App\Services\Contracts;

use App\Models\File;
use App\Models\Task;

interface TaskServiceContract
{
    public function list (array $inputs = []);

    public function get (int|string $id, array $inputs = []);

    public function store (array $inputs);

    public function update (int|string $id, array $inputs);

    public function delete (int|string $id);

    public function attachFiles (Task $task, array $inputs);

    public function detachFile (Task $task, File $file);
}
