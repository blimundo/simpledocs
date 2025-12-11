<?php

declare(strict_types=1);

use App\Actions\Permissions\ListPermissionsAction;
use App\Enums\PermissionsEnum;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'actions', 'permissions');

it('lists all permissions', function () {
    Permission::factory()->count(20)->create();

    $permissions = resolve(ListPermissionsAction::class)->handle();

    expect($permissions)->toHaveCount(20)
        ->and($permissions[0])->toBeInstanceOf(Permission::class);
});

it('returns an empty array when there are no permissions', function () {
    $permissions = resolve(ListPermissionsAction::class)->handle();

    expect($permissions)->toBeEmpty();
});

it('searches permissions by name', function () {
    Permission::factory(3)->sequence(
        ['name' => PermissionsEnum::ROLES_CREATE->value],
        ['name' => PermissionsEnum::ROLES_DELETE->value],
        ['name' => PermissionsEnum::ROLES_EDIT->value],
    )->create();

    $permissions = resolve(ListPermissionsAction::class)->handle(
        search: 'DELETE'
    );

    expect($permissions)->toHaveCount(1)
        ->and($permissions->first()->name)->toBe(PermissionsEnum::ROLES_DELETE->value);
});
