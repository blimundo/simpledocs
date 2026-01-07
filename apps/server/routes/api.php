<?php

declare(strict_types=1);

use App\Http\Controllers;
use App\Http\Controllers\Permissions;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResources([
        'roles' => Permissions\RoleController::class,
        'disks' => Controllers\Disks\DiskController::class,
    ]);

    Route::apiResources([
        'disk-types' => Controllers\Disks\DiskTypeController::class,
    ], ['only' => ['index', 'show']]);

    Route::apiResources([
        'permissions' => Permissions\PermissionController::class,
    ], ['only' => ['index']]);
});
