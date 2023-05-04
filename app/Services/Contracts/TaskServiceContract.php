<?php

namespace App\Services\Contracts;

use App\Models\File;
use App\Models\Task;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface TaskServiceContract
{
    public function search (array $inputs = []);

    public function list (array $inputs = []);

    public function get (int|string $id, array $inputs = []);

    public function store (array $inputs);

    public function update (int|string $id, array $inputs);

    public function delete (int|string $id);

    public function attachFiles (Task $task, array $attachments);

    public function detachFile (Task $task, File $file);

    public function submitReport (Task $task, UploadedFile $uploadedFile);

    public function deleteReport (Task $task);

    public function storeComment (Task $task, array $inputs);

    public function history (Task $task);

}
