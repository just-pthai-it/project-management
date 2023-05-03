<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\CreateProjectPostRequest;
use App\Http\Requests\Project\UpdateProjectPatchRequest;
use App\Models\Project;
use App\Services\Contracts\ProjectServiceContract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index (Request $request) : JsonResponse
    {
        return $this->projectService->list($request->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateProjectPostRequest $request
     * @return JsonResponse
     */
    public function store (CreateProjectPostRequest $request) : JsonResponse
    {
        return $this->projectService->store($request->validated());
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
        return $this->projectService->update($project, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Project $project
     * @return Response
     */
    public function destroy (Project $project) : Response
    {
        return $this->projectService->delete($project);
    }
}
