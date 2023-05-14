<?php

namespace App\Http\Controllers;

use App\Models\ProjectStatus;
use App\Services\Contracts\ProjectStatusServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectStatusController extends Controller
{
    private ProjectStatusServiceContract $projectStatusService;

    /**
     * @param ProjectStatusServiceContract $projectStatusService
     */
    public function __construct (ProjectStatusServiceContract $projectStatusService)
    {
        $this->projectStatusService = $projectStatusService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index () : JsonResponse
    {
        return $this->projectStatusService->list();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function store (Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ProjectStatus $projectStatus
     * @return Response
     */
    public function show (ProjectStatus $projectStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \App\Models\ProjectStatus $projectStatus
     * @return Response
     */
    public function update (Request $request, ProjectStatus $projectStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ProjectStatus $projectStatus
     * @return Response
     */
    public function destroy (ProjectStatus $projectStatus)
    {
        //
    }
}
