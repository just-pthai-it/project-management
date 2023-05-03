<?php

namespace App\Services;

use App\Helpers\CusResponse;
use App\Http\Resources\Task\TaskCollection;
use App\Models\File;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryContract;
use App\Services\Contracts\FileServiceContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class TaskService implements Contracts\TaskServiceContract
{
    private FileServiceContract $fileService;
    private TaskRepositoryContract $taskRepository;

    public function __construct (TaskRepositoryContract $taskRepository, Contracts\FileServiceContract $fileService)
    {
        $this->taskRepository = $taskRepository;
        $this->fileService    = $fileService;
    }

    public function list (array $inputs = []) : JsonResponse
    {
        $tasks = auth()->user()->assignedTasks()
                       ->filter($inputs)
                       ->with(['project:id,code,name', 'status'])
                       ->paginate($inputs['per_page'] ?? 10,
                                  ['tasks.id', 'name', 'project_id', 'starts_at', 'ends_at', 'status_id']);

        return (new TaskCollection($tasks))->response();
    }

    public function get (int|string $id, array $inputs = []) {}

    public function store (array $inputs) {}

    public function update (int|string $id, array $inputs) {}

    public function delete (int|string $id) {}

    public function attachFiles (Task $task, array $inputs) : JsonResponse
    {
        $filesInfo = $this->__uploadAttachFiles($inputs['attachments'] ?? [], "task_{$task->id}/attach_files");
        $files     = $this->__storeFiles($task, $filesInfo);
        return CusResponse::createSuccessful($files);
    }


    private function __uploadAttachFiles (array $files, string $path = '') : array
    {
        return $this->fileService->putUploadedFilesAndKeepName($files, $path);
    }

    private function __storeFiles (Task $task, array $filesInfo) : array
    {
        $files = [];
        foreach ($filesInfo as $fileInfo)
        {
            $files[] = $task->files()->create($fileInfo);
        }

        return $files;
    }

    public function detachFile (Task $task, File $file) : JsonResponse
    {
        $this->__deleteUploadedFile($file->file_path, $file->disk);
        $this->__deleteFile($file);
        return CusResponse::successfulWithNoData();
    }

    private function __deleteUploadedFile (string $filePath, string $disk) : void
    {
        $this->fileService->deleteFile($filePath, $disk);
    }

    private function __deleteFile (File $file) : void
    {
        $file->delete();
    }
}
