<?php

namespace App\Http\Controllers;

use App\CommandBus\Commands\Project\CreateProjectCommand;
use App\CommandBus\Commands\Project\PaginateProjectsListCommand;
use App\CommandBus\Commands\Project\UpdateProjectCommand;
use App\CommandBus\Middlewares\TransactionMiddleware;
use App\Http\Requests\Project\CreateProjectPostRequest;
use App\Http\Requests\Project\UpdateProjectPatchRequest;
use App\Http\Resources\Project\ProjectCollection;
use App\Models\Project;
use App\Services\Contracts\ProjectServiceContract;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends BaseController
{
    private ProjectServiceContract $projectService;

    /**
     * @param ProjectServiceContract $projectService
     */
    public function __construct (ProjectServiceContract $projectService)
    {
        $this->authorizeResource(Project::class, 'project');
        $this->projectService = $projectService;
    }

    public function search (Request $request) : JsonResponse
    {
        $this->authorize('search', Project::class);
        return $this->projectService->search($request->all());
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index (Request $request) : JsonResponse
    {
        $paginateProjectsListCommand = new PaginateProjectsListCommand($request->all());
        $projects = $this->dispatchCommand($paginateProjectsListCommand);
        return (new ProjectCollection($projects))->response();
    }

    public function indexGanttChart (Request $request) : JsonResponse
    {
        return $this->projectService->listGanttChart($request->all());
    }

    public function statistics (Request $request) : JsonResponse
    {
        return $this->projectService->statistics($request->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateProjectPostRequest $request
     * @return JsonResponse
     */
    public function store (CreateProjectPostRequest $request) : JsonResponse
    {
        $createProjectCommand  = new CreateProjectCommand($request->validated());
        $transactionMiddleware = new TransactionMiddleware();
        $project               = $this->dispatchCommand($createProjectCommand, [$transactionMiddleware]);
        return response()->jsonWrap($project, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Project $project
     * @return JsonResponse
     */
    public function show (Project $project) : JsonResponse
    {
        return $this->projectService->get($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProjectPatchRequest $request
     * @param Project                   $project
     * @return JsonResponse
     */
    public function update (UpdateProjectPatchRequest $request, Project $project) : JsonResponse
    {
        $updateProjectCommand  = new UpdateProjectCommand($project, $request->validated());
        $transactionMiddleware = new TransactionMiddleware();
        $project               = $this->dispatchCommand($updateProjectCommand, [$transactionMiddleware]);
        return response()->jsonWrap($project);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Project $project
     * @return JsonResponse
     */
    public function destroy (Project $project) : JsonResponse
    {
        return $this->projectService->delete($project);
    }

    /**
     * @throws AuthorizationException
     */
    public function history (Project $project) : JsonResponse
    {
        $this->authorize('history', $project);
        return $this->projectService->history($project);
    }
}
