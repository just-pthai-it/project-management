<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\Contracts\ProjectServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectUserController extends Controller
{
    private ProjectServiceContract $projectService;

    /**
     * @param ProjectServiceContract $projectService
     */
    public function __construct (ProjectServiceContract $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Project $project
     * @return JsonResponse
     */
    public function index(Request $request, Project $project) : JsonResponse
    {
        return $this->projectService->listUsers($project, $request->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return Response
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return Response
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return Response
     */
    public function destroy(Project $project)
    {
        //
    }
}
