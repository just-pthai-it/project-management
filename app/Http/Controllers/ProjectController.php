<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\CreateProjectPostRequest;
use App\Http\Requests\Project\UpdateProjectPatchRequest;
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
     * @param int $id
     * @return JsonResponse
     */
    public function show (int $id) : JsonResponse
    {
        return $this->projectService->get($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProjectPatchRequest $request
     * @param int                       $id
     * @return JsonResponse
     */
    public function update (UpdateProjectPatchRequest $request, int $id) : JsonResponse
    {
        return $this->projectService->update($id, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy (int $id) : Response
    {
        return $this->projectService->delete($id);
    }
}
