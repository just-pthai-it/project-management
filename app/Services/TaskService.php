<?php

namespace App\Services;

use App\Helpers\CusResponse;
use App\Http\Resources\Task\TaskCollection;
use App\Models\File;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryContract;
use App\Services\Contracts\FileServiceContract;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

    public function attachFiles (Task $task, array $attachments) : JsonResponse
    {
        $filesInfo = $this->fileService->putUploadedFilesAndKeepName($attachments, "task_{$task->id}/attach_files");
        $files     = $this->__storeFiles($task, $filesInfo);
        return CusResponse::createSuccessful($files);
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
        $this->fileService->deleteFile($file->file_path, $file->disk);
        $file->delete();
        return CusResponse::successfulWithNoData();
    }

    public function submitReport (Task $task, UploadedFile $uploadedFile) : JsonResponse
    {
        $taskUserPair = $task->taskUserPairs()->where('user_id', '=', auth()->id())->first();
        $file         = $taskUserPair->file;
        if (!is_null($file))
        {
            $this->fileService->deleteFile($file->file_path, $file->disk);
            $file->delete();
        }

        $uploadFileInfo = $this->fileService->putUploadedFileAndKeepName($uploadedFile, "task_{$task->id}/reports");
        $taskUserPair->file()->create($uploadFileInfo);

        return CusResponse::successfulWithNoData();
    }

    public function deleteReport (Task $task) : JsonResponse
    {
        $taskUserPair = $task->taskUserPairs()->where('user_id', '=', auth()->id())->first();
        $file = $taskUserPair->file;
        $this->fileService->deleteFile($file->file_path, $file->disk);
        $file->delete();
        return CusResponse::successfulWithNoData();
    }
}
