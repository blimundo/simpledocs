<?php

declare(strict_types=1);

use App\Actions\Permissions\Roles\CreateRoleAction;
use App\Data\Permissions\Roles\CreateRoleData;
use App\Enums\PermissionsEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'actions', 'permissions', 'roles');

beforeEach(function () {
    $this->action = resolve(CreateRoleAction::class);
});

it('creates a new role', function () {
    $role = $this->action->handle(
        new CreateRoleData(name: 'Administrator')
    );

    expect($role)->toBeInstanceOf(Role::class)
        ->and($role->name)->toBe('Administrator')
        ->and($role->permissions)->toBeInstanceOf(Illuminate\Database\Eloquent\Collection::class)
        ->and($role->permissions->isEmpty())->toBeTrue();
});

it('creates a new role with permissions', function () {
    Permission::factory(2)->sequence(
        ['name' => PermissionsEnum::ROLES_CREATE],
        ['name' => PermissionsEnum::ROLES_EDIT],
    )->create();

    $role = $this->action->handle(
        new CreateRoleData(name: 'Administrator', permissions: [
            PermissionsEnum::ROLES_CREATE,
            PermissionsEnum::ROLES_EDIT->value,
        ])
    );

    expect($role->permissions->pluck('name'))->toContain(
        PermissionsEnum::ROLES_CREATE->value,
        PermissionsEnum::ROLES_EDIT->value
    );
})->depends('it creates a new role');
