<?php

declare(strict_types=1);

use App\Http\Controllers\Permissions;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResources([
        'roles' => Permissions\RoleController::class,
    ]);

    Route::apiResources([
        'permissions' => Permissions\PermissionController::class,
    ], ['only' => ['index']]);
});
