<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'models', 'permission');

it('can be converted to an array', function () {
    $role = Role::factory()->create()->refresh();

    expect(array_keys($role->toArray()))
        ->toBe([
            'id',
            'uuid',
            'name',
            'guard_name',
            'created_at',
            'updated_at',
        ]);
});

it('can be retrieved by uuid', function () {
    $role = Role::factory()->create();

    $foundRole = Role::findByUuid($role->uuid);

    expect($foundRole)->not->toBeNull()
        ->and($foundRole->id)->toBe($role->id);

    $notFoundRole = Role::findByUuid('non-existing-uuid');

    expect($notFoundRole)->toBeNull();
});

it('can assign permissions', function () {
    Permission::factory()->create(['name' => 'edit articles']);
    $role = Role::factory()->create();

    $role->givePermissionTo('edit articles');

    expect($role->hasPermissionTo('edit articles'))->toBeTrue();
});

it('can revoke permissions', function () {
    Permission::factory()->create(['name' => 'edit articles']);
    $role = Role::factory()->create();
    $role->givePermissionTo('edit articles');

    expect($role->hasPermissionTo('edit articles'))->toBeTrue();

    $role->revokePermissionTo('edit articles');

    expect($role->hasPermissionTo('edit articles'))->toBeFalse();
});
