<?php

declare(strict_types=1);

namespace App\Http\Controllers\Disks;

use App\Actions\Disks\DiskTypes\ListDiskTypesAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Disks\DiskTypeListResource;
use App\Http\Resources\Disks\DiskTypeResource;
use App\Models\DiskType;
use Illuminate\Http\Request;

final class DiskTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, ListDiskTypesAction $action)
    {
        $diskTypes = $action->handle(
            $request->query('search')
        );

        return DiskTypeListResource::collection($diskTypes);
    }

    /**
     * Display the specified resource.
     *
     * @return DiskTypeResource
     */
    public function show(DiskType $diskType)
    {
        return new DiskTypeResource($diskType);
    }
}
