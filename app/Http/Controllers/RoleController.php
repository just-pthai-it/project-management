<?php

namespace App\Http\Controllers;

use App\CommandBus\Commands\Role\CreateRoleCommand;
use App\CommandBus\Commands\Role\DeleteRoleCommand;
use App\CommandBus\Commands\Role\GetListRolesCommand;
use App\CommandBus\Commands\Role\GetRoleCommand;
use App\CommandBus\Commands\Role\UpdateRoleCommand;
use App\Http\Requests\Role\StoreRolePostRequest;
use App\Http\Requests\Role\UpdateRolePostRequest;
use App\Http\Resources\Role\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends BaseController
{
    public function __construct ()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        $getListRolesCommand = new GetListRolesCommand();
        $roles = $this->dispatchCommand($getListRolesCommand);
        return RoleResource::collection($roles)->response();
    }

    public function show (Role $role) : JsonResponse
    {
        $getRoleCommand = new GetRoleCommand($role);
        $role = $this->dispatchCommand($getRoleCommand);
        return (new RoleResource($role))->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRolePostRequest $request
     * @return JsonResponse
     */
    public function store(StoreRolePostRequest $request) : JsonResponse
    {
        $createRoleCommand = new CreateRoleCommand($request->validated());
        $role = $this->dispatchCommand($createRoleCommand);
        return response()->jsonWrap($role, Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRolePostRequest $request
     * @param Role                  $role
     * @return JsonResponse
     */
    public function update(UpdateRolePostRequest $request, Role $role) : JsonResponse
    {
        $updateRoleCommand = new UpdateRoleCommand($role, $request->validated());
        $this->dispatchCommand($updateRoleCommand);
        return response()->jsonWrap();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Role $role
     * @return JsonResponse
     */
    public function destroy(Role $role) : JsonResponse
    {
        $deleteRoleCommand = new DeleteRoleCommand($role);
        $this->dispatchCommand($deleteRoleCommand);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
