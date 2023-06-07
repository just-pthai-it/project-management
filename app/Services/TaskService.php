<?php

namespace App\Services;

use App\Events\ObjectResourceUpdatedEvent;
use App\Events\UserCommentedEvent;
use App\Helpers\CusResponse;
use App\Http\Resources\ActivityLog\ActivityLogResource;
use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Task\TaskCollection;
use App\Http\Resources\Task\TaskStatisticsCollection;
use App\Models\Comment;
use App\Models\File;
use App\Models\Notification;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryContract;
use App\Services\Contracts\FileServiceContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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

    public function statistics (array $inputs) : JsonResponse
    {
        if (!isset($inputs['start_at']) && !isset($inputs['end_at']))
        {
            $inputs = ['start_at' => Carbon::now()->firstOfMonth(), 'end_at' => Carbon::now()->endOfMonth()];
        }

        if (!isset($inputs['start_at']) || !isset($inputs['end_at']))
        {
            abort(400);
        }

        $groupTasksCount = DB::table('tasks')
                             ->where('starts_at', '>=', $inputs['start_at'])
                             ->where('ends_at', '<=', $inputs['end_at'])
                             ->groupBy('status_id')
                             ->selectRaw('count(*) as tasks_count, status_id')->get();

        return (new TaskStatisticsCollection($groupTasksCount))->response();
    }

    public function get (int|string $id, array $inputs = []) {}

    public function store (array $inputs) {}

    public function update (int|string $id, array $inputs) {}

    public function delete (int|string $id) {}

    public function attachFiles (Task $task, array $attachments) : JsonResponse
    {
        $filesInfo = $this->fileService->putUploadedFilesAndKeepName($attachments, "task_{$task->id}/attach_files");
        $files     = $this->__storeFiles($task, $filesInfo);
        event(new ObjectResourceUpdatedEvent($task, auth()->user(), 'attached_file'));
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
        event(new ObjectResourceUpdatedEvent($task, auth()->user(), 'detached_file'));
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
        $fileModel      = $taskUserPair->file()->create($uploadFileInfo);
        event(new ObjectResourceUpdatedEvent($task, auth()->user(), 'submitted_report'));

        return CusResponse::successful($fileModel);
    }

    public function deleteReport (Task $task) : JsonResponse
    {
        $taskUserPair = $task->taskUserPairs()->where('user_id', '=', auth()->id())->first();
        $file         = $taskUserPair->file;
        $this->fileService->deleteFile($file->file_path, $file->disk);
        $file->delete();
        event(new ObjectResourceUpdatedEvent($task, auth()->user(), 'deleted_report'));

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

//            event(new UserCommentedEvent($task, $comment, $previousComment));

        }
        else
        {
            $comment = $task->comments()->create($inputs + ['deep_level' => 1]);
        }

        return CusResponse::createSuccessful(['id' => $comment->id]);
    }

    public function listComments (Task $task) : JsonResponse
    {
        $comments = $task->comments()->with(['user'])->latest()->orderByDesc('id')->get();
        return CommentResource::collection($comments)->response();
    }

    public function history (Task $task) : JsonResponse
    {
        $task->load(['activityLogs.user:id,name,email,avatar']);
        return ActivityLogResource::collection($task->activityLogs)->response();
    }
}
