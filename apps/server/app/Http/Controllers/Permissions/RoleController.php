<?php

declare(strict_types=1);

namespace App\Http\Controllers\Permissions;

use App\Actions\Permissions\Roles\CreateRoleAction;
use App\Actions\Permissions\Roles\SearchRolesAction;
use App\Actions\Permissions\Roles\UpdateRoleAction;
use App\Data\Permissions\Roles\CreateRoleData;
use App\Data\Permissions\Roles\SearchRolesData;
use App\Data\Permissions\Roles\UpdateRoleData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Permissions\Roles\SearchRolesRequest;
use App\Http\Requests\Permissions\Roles\StoreRoleRequest;
use App\Http\Requests\Permissions\Roles\UpdateRoleRequest;
use App\Http\Resources\Permissions\RoleResource;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;

final class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(SearchRolesRequest $request, SearchRolesAction $action)
    {
        $roles = $action->handle(
            SearchRolesData::from($request->all())
        );

        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RoleResource
     */
    public function store(StoreRoleRequest $request, CreateRoleAction $action)
    {
        $role = $action->handle(
            CreateRoleData::from($request->validated())
        );

        $role->loadCount(['permissions', 'users']);

        return new RoleResource($role);
    }

    /**
     * Display the specified resource.
     *
     * @return RoleResource
     */
    public function show(Role $role)
    {
        Gate::authorize('view', $role);

        return new RoleResource($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return RoleResource
     */
    public function update(UpdateRoleRequest $request, Role $role, UpdateRoleAction $action)
    {
        $role = $action->handle(
            $role,
            UpdateRoleData::from($request->all())
        );

        $role->loadCount(['permissions', 'users']);

        return new RoleResource($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        Gate::authorize('delete', $role);

        $role->delete();

        return response()->noContent();
    }
}
