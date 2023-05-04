<?php

namespace App\Services;

use App\Events\ObjectResourceUpdated;
use App\Events\UserCommented;
use App\Helpers\CusResponse;
use App\Http\Resources\ActivityLog\ActivityLogResource;
use App\Http\Resources\Task\TaskCollection;
use App\Models\Comment;
use App\Models\File;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryContract;
use App\Services\Contracts\FileServiceContract;
use Illuminate\Database\Eloquent\Collection;
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

    public function search (array $inputs = []) : JsonResponse
    {
        $tasks = auth()->user()->assignedTasks()->filter($inputs)->get(['tasks.id', 'tasks.name', 'project_id']);
        return CusResponse::successful($tasks);
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
        event(new ObjectResourceUpdated($task, auth()->user(), 'attached', 'files'));
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
        event(new ObjectResourceUpdated($task, auth()->user(), 'detached', 'files', 'from'));
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
        event(new ObjectResourceUpdated($task, auth()->user(), 'submitted', 'a report'));

        return CusResponse::successfulWithNoData();
    }

    public function deleteReport (Task $task) : JsonResponse
    {
        $taskUserPair = $task->taskUserPairs()->where('user_id', '=', auth()->id())->first();
        $file         = $taskUserPair->file;
        $this->fileService->deleteFile($file->file_path, $file->disk);
        $file->delete();
        event(new ObjectResourceUpdated($task, auth()->user(), 'removed', 'report', 'from'));

        return CusResponse::successfulWithNoData();
    }

    public function storeComment (Task $task, array $inputs) : JsonResponse
    {
        if (isset($inputs['comment_id']))
        {
            $previousComment = Comment::query()->find($inputs['comment_id']);
            if ($previousComment->deep_level == 1)
            {
                $comment = $previousComment->comments()->create($inputs + ['deep_level' => 2]);
            }
            else
            {
                $comment = $previousComment->replicate()->fill($inputs + ['deep_level' => 2]);
                $comment->save();
            }
        }
        else
        {
            $comment = $task->comments()->create($inputs + ['deep_level' => 1]);
            event(new UserCommented($task, auth()->user(), $comment->id));
        }

        return CusResponse::createSuccessful(['id' => $comment->id]);
    }

    public function history (Task $task) : JsonResponse
    {
        $task->load(['activityLogs', 'activityLogs.comment']);
        return ActivityLogResource::collection($task->activityLogs)->response();
    }
}
