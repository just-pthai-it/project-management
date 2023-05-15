<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\StoreRolePostRequest;
use App\Http\Requests\Role\UpdateRolePostRequest;
use App\Models\Role;
use App\Services\Contracts\RoleServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    private RoleServiceContract $roleService;

    /**
     * @param RoleServiceContract $roleService
     */
    public function __construct (RoleServiceContract $roleService)
    {
        $this->roleService = $roleService;
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        return $this->roleService->list();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRolePostRequest $request
     * @return JsonResponse
     */
    public function store(StoreRolePostRequest $request) : JsonResponse
    {
        return $this->roleService->store($request->validated());
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
        return $this->roleService->update($role, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Role $role
     * @return JsonResponse
     */
    public function destroy(Role $role) : JsonResponse
    {
        return $this->roleService->delete($role);
    }
}
