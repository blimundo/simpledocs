<?php

declare(strict_types=1);

use App\Actions\Permissions\Roles\UpdateRoleAction;
use App\Data\Permissions\Roles\UpdateRoleData;
use App\Enums\PermissionsEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'actions', 'permissions', 'roles');

beforeEach(function () {
    Permission::factory()->createMany([
        ['name' => PermissionsEnum::ROLES_CREATE],
        ['name' => PermissionsEnum::ROLES_EDIT],
    ]);

    $this->role = Role::factory()->create();
    $this->action = app(UpdateRoleAction::class);

    $this->role->givePermissionTo(PermissionsEnum::ROLES_CREATE);
});

it('can update an existing role', function () {
    $updatedRole = $this->action->handle(
        role: $this->role,
        data: new UpdateRoleData(name: 'Updated Role Name'),
    );

    expect($updatedRole)->toBeInstanceOf(Role::class)
        ->and($updatedRole->uuid)->toBe($this->role->uuid)
        ->and($updatedRole->name)->toBe('Updated Role Name')
        ->and($updatedRole->permissions()->count())->toBe(1)
        ->and($updatedRole->permissions->first()->name)->toBeEnum(PermissionsEnum::ROLES_CREATE);

    expect(Role::count())->toBe(1);
});

it('can update a role and sync permissions', function () {
    $updatedRole = $this->action->handle(
        role: $this->role,
        data: new UpdateRoleData(name: 'Role with Permissions', permissions: [
            PermissionsEnum::ROLES_EDIT,
        ]),
    );

    expect($updatedRole->uuid)->toBe($this->role->uuid)
        ->and($updatedRole->name)->toBe('Role with Permissions')
        ->and($updatedRole->permissions()->count())->toBe(1)
        ->and($updatedRole->permissions->first()->name)->toBeEnum(PermissionsEnum::ROLES_EDIT);

    expect(Role::count())->toBe(1);
});
