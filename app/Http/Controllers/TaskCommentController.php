<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\CreateCommentPostRequest;
use App\Models\Comment;
use App\Models\Task;
use App\Services\Contracts\TaskServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskCommentController extends Controller
{
    private TaskServiceContract $taskService;

    /**
     * @param TaskServiceContract $taskService
     */
    public function __construct (TaskServiceContract $taskService)
    {
        $this->taskService = $taskService;
        $this->authorizeResource(Comment::class, 'comment');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function index (Task $task) : JsonResponse
    {
        return $this->taskService->listComments($task);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateCommentPostRequest $request
     * @param Task                     $task
     * @return JsonResponse
     */
    public function store (CreateCommentPostRequest $request, Task $task) : JsonResponse
    {
        return $this->taskService->storeComment($task, $request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Task $task
     * @return Response
     */
    public function show (Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task         $task
     * @return Response
     */
    public function update (Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Task $task
     * @return Response
     */
    public function destroy (Task $task)
    {
        //
    }
}
