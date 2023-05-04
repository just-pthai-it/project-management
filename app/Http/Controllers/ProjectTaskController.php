<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\CreateTaskPostRequest;
use App\Http\Requests\Task\UpdateTaskPatchRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\Contracts\ProjectServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectTaskController extends Controller
{
    private ProjectServiceContract $projectService;

    /**
     * @param ProjectServiceContract $projectService
     */
    public function __construct (ProjectServiceContract $projectService)
    {
        $this->authorizeResource(Task::class, 'task');
        $this->projectService = $projectService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Project $project
     * @return JsonResponse
     */
    public function index (Request $request, Project $project) : JsonResponse
    {
        return $this->projectService->listTasks($project, $request->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateTaskPostRequest $request
     * @param Project               $project
     * @return JsonResponse
     */
    public function store (CreateTaskPostRequest $request, Project $project) : JsonResponse
    {
        return $this->projectService->storeTask($project, $request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param Project $project
     * @param Task    $task
     * @return JsonResponse
     */
    public function show (Project $project, Task $task) : JsonResponse
    {
        return $this->projectService->getTask($project, $task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTaskPatchRequest $request
     * @param Project                $project
     * @param Task                   $task
     * @return JsonResponse
     */
    public function update (UpdateTaskPatchRequest $request, Project $project, Task $task) : JsonResponse
    {
//        return $this->projectService->updateTask($project, $task, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy ($id)
    {
        //
    }
}
