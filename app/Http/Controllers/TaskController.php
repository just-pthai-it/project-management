<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\AttachFilesPostRequest;
use App\Http\Requests\Task\SubmitReportPostRequest;
use App\Models\File;
use App\Models\Task;
use App\Services\Contracts\TaskServiceContract;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private TaskServiceContract $taskService;

    /**
     * @param TaskServiceContract $taskService
     */
    public function __construct (TaskServiceContract $taskService)
    {
        $this->authorizeResource(Task::class, 'task');
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index (Request $request) : JsonResponse
    {
        return $this->taskService->list($request->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store (Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show ($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy ($id)
    {
        //
    }

    /**
     * @throws AuthorizationException
     */
    public function attachFiles (AttachFilesPostRequest $request, Task $task)
    {
        $this->authorize('attach-files', $task);
        return $this->taskService->attachFiles($task, $request->attachments);
    }

    /**
     * @throws AuthorizationException
     */
    public function detachFile (Task $task, File $file)
    {
        $this->authorize('detach-file', $task);
        return $this->taskService->detachFile($task, $file);
    }

    public function submitReport (SubmitReportPostRequest $request, Task $task) : JsonResponse
    {
        return $this->taskService->submitReport($task, $request->file('report'));
    }

    public function destroyReport (Task $task) : JsonResponse
    {
        return $this->taskService->deleteReport($task);
    }

        /**
     * @throws AuthorizationException
     */
    public function history (Task $task) : JsonResponse
    {
        $this->authorize('history', $task);
        return $this->taskService->history($task);
    }
}
