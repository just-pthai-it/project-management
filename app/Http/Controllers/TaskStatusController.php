<?php

namespace App\Http\Controllers;

use App\Models\TaskStatus;
use App\Services\Contracts\TaskStatusServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskStatusController extends Controller
{
    private TaskStatusServiceContract $taskStatusService;

    /**
     * @param TaskStatusServiceContract $taskStatusService
     */
    public function __construct (TaskStatusServiceContract $taskStatusService)
    {
        $this->taskStatusService = $taskStatusService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index () : JsonResponse
    {
        return $this->taskStatusService->list();
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
     * @param \App\Models\TaskStatus $taskStatus
     * @return Response
     */
    public function show (TaskStatus $taskStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\TaskStatus   $taskStatus
     * @return Response
     */
    public function update (Request $request, TaskStatus $taskStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\TaskStatus $taskStatus
     * @return Response
     */
    public function destroy (TaskStatus $taskStatus)
    {
        //
    }
}
