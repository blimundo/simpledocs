<?php

declare(strict_types=1);

namespace App\Http\Controllers\Permissions;

use App\Actions\Permissions\ListPermissionsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Permissions\PermissionResource;
use Illuminate\Http\Request;

final class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, ListPermissionsAction $action)
    {
        $permissions = $action->handle(
            search: $request->query('search'),
        );

        return PermissionResource::collection($permissions);
    }
}
