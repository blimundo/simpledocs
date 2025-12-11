<?php

declare(strict_types=1);

use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'models', 'permission');

it('can be converted to an array', function () {
    $permission = Permission::factory()->create()->refresh();

    expect(array_keys($permission->toArray()))
        ->toBe([
            'id',
            'name',
            'guard_name',
            'created_at',
            'updated_at',
        ]);
});
