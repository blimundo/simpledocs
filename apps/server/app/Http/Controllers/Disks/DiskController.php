<?php

declare(strict_types=1);

namespace App\Http\Controllers\Disks;

use App\Actions\Disks\Disks\CreateDiskAction;
use App\Actions\Disks\Disks\DeleteDiskAction;
use App\Actions\Disks\Disks\SearchDisksAction;
use App\Actions\Disks\Disks\UpdateDiskAction;
use App\Data\Disks\Disks\CreateDiskData;
use App\Data\Disks\Disks\SearchDisksData;
use App\Data\Disks\Disks\UpdateDiskData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Disks\Disks\SearchDisksRequest;
use App\Http\Requests\Disks\Disks\StoreDiskRequest;
use App\Http\Requests\Disks\Disks\UpdateDiskRequest;
use App\Http\Resources\Disks\DiskListResource;
use App\Http\Resources\Disks\DiskResource;
use App\Models\Disk;
use Illuminate\Support\Facades\Gate;

final class DiskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(SearchDisksRequest $request, SearchDisksAction $action)
    {
        $disks = $action->handle(
            SearchDisksData::from($request->all())
        );

        return DiskListResource::collection($disks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return DiskResource
     */
    public function store(StoreDiskRequest $request, CreateDiskAction $action)
    {
        $disk = $action->handle(
            CreateDiskData::from($request->validated())
        );

        $disk->load('type');

        return new DiskResource($disk);
    }

    /**
     * Display the specified resource.
     *
     * @return DiskResource
     */
    public function show(Disk $disk)
    {
        Gate::authorize('view', $disk);

        $disk->load('type');

        return new DiskResource($disk);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return DiskResource
     */
    public function update(UpdateDiskRequest $request, Disk $disk, UpdateDiskAction $action)
    {
        $disk = $action->handle(
            $disk,
            UpdateDiskData::from($request->validated())
        );

        return new DiskResource($disk);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Disk $disk, DeleteDiskAction $action)
    {
        Gate::authorize('delete', $disk);

        $action->handle($disk);

        return response()->noContent();
    }
}
