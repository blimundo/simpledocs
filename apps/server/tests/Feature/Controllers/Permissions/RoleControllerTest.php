<?php

declare(strict_types=1);

use App\Enums\PermissionsEnum;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

uses(RefreshDatabase::class)->group('feature', 'http', 'controllers', 'permissions', 'roles');

beforeEach(function () {
    $permisisons = Permission::factory()->createMany([
        ['name' => PermissionsEnum::ROLES_LIST],
        ['name' => PermissionsEnum::ROLES_CREATE],
        ['name' => PermissionsEnum::ROLES_VIEW],
        ['name' => PermissionsEnum::ROLES_EDIT],
        ['name' => PermissionsEnum::ROLES_DELETE],
    ]);

    $this->user = User::factory()->withPermission($permisisons)->create();
});

describe('list', function () {
    it('can search roles by name', function () {
        $roles = Role::factory()->createMany([
            ['name' => 'Administrator'],
            ['name' => 'Viewer'],
            ['name' => 'Editor'],
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('roles.index', ['search' => 'DITOR']));

        expect($response->status())->toBe(Response::HTTP_OK)
            ->and($response->headers->get('content-type'))->toContain('application/json');

        expect($response->json())->toHaveKeys(['data', 'links', 'meta']);

        expect($response->json('data.0'))->toBe([
            'uuid' => $roles->last()->uuid,
            'name' => 'Editor',
            'usersCount' => 0,
            'permissionsCount' => 0,
            'createdAt' => $roles->last()->created_at->toJSON(),
            'updatedAt' => $roles->last()->updated_at->toJSON(),
        ]);
    });

    it('validate name field when searching roles', function (string|int|null $name, ?string $expectedError = null) {
        $response = test()->actingAs(test()->user)
            ->getJson(route('roles.index', ['search' => $name]));

        $expectedError
            ? expect($response->json('errors.search.0'))->toBe($expectedError)
            : expect($response->json('errors.search'))->toBeNull();
    })->depends('it can search roles by name')->with('search name validation data');
});

describe('create', function () {
    it('can create a new role with permissions', function () {
        $response = $this->actingAs($this->user)
            ->postJson(route('roles.store'), [
                'name' => 'new-role',
                'permissions' => [
                    PermissionsEnum::ROLES_VIEW->value,
                    PermissionsEnum::ROLES_LIST->value,
                ],
            ]);

        expect($response->status())->toBe(Response::HTTP_CREATED)
            ->and($response->headers->get('content-type'))->toContain('application/json');

        expect($response->json('data'))->toBe([
            'uuid' => $response->json('data.uuid'),
            'name' => 'new-role',
            'usersCount' => 0,
            'permissionsCount' => 2,
            'createdAt' => $response->json('data.createdAt'),
            'updatedAt' => $response->json('data.updatedAt'),
        ]);

        expect($response->json('data'))->not->toHaveKeys(['id', 'permissions', 'users']);

        expect(Role::count())->toBe(1);

        $foundRole = Role::first();
        expect($foundRole->uuid)->toBeUuid()
            ->and($foundRole->name)->toBe('new-role')
            ->and($foundRole->guard_name)->toBe('web')
            ->and($foundRole->permissions->pluck('name'))->toMatchArray([
                PermissionsEnum::ROLES_VIEW->value,
                PermissionsEnum::ROLES_LIST->value,
            ])
            ->and($foundRole->users)->toHaveCount(0);
    });

    it('validate name field when creating a new role', function (string|int|null $name, ?string $expectedError = null) {
        $response = $this->actingAs($this->user)
            ->postJson(route('roles.store'), ['name' => $name]);

        $expectedError
            ? expect($response->json('errors.name.0'))->toBe($expectedError)
            : expect($response->json('errors.name'))->toBeNull();
    })->depends('it can create a new role with permissions')->with('name validation data');

    it('validate permissions field when creating a new role', function (array|string|null $permissions, ?string $expectedError, string $key) {
        $response = $this->actingAs($this->user)
            ->postJson(route('roles.store'), ['permissions' => $permissions]);

        $expectedError
            ? expect($response->json('errors')[$key][0])->toBe($expectedError)
            : expect($response->json('errors.permissions'))->toBeNull();
    })->depends('it can create a new role with permissions')->with('permissions validation data');
});

describe('retrieve', function () {
    beforeEach(function () {
        $this->role = Role::factory()->create();
    });

    it('can retrieve a role', function () {
        $this->role->givePermissionTo(Permission::factory(5)->create());
        $this->role->users()->attach(User::factory(3)->create());

        $response = $this->actingAs($this->user)
            ->getJson(route('roles.show', ['role' => $this->role->uuid]));

        expect($response->status())->toBe(Response::HTTP_OK)
            ->and($response->headers->get('content-type'))->toBe('application/json');

        expect($response->json('data'))->toBe([
            'uuid' => $this->role->uuid,
            'name' => $this->role->name,
            'usersCount' => 3,
            'permissionsCount' => 5,
            'createdAt' => $this->role->created_at->toJSON(),
            'updatedAt' => $this->role->updated_at->toJSON(),
        ]);

        expect($response->json('data'))->not->toHaveKeys(['id', 'permissions', 'users']);
    });

    it('returns 404 when retrieving a non-existing role', function () {
        $response = $this->actingAs($this->user)
            ->getJson(route('roles.show', ['role' => 'non-existing-uuid']));

        expect($response->status())->toBe(Response::HTTP_NOT_FOUND);
    });
});

describe('update', function () {

    beforeEach(function () {
        $this->role = Role::factory()->create();
    });

    it('can update a role with permissions', function () {
        $response = $this->actingAs($this->user)
            ->putJson(route('roles.update', ['role' => $this->role->uuid]), [
                'name' => 'Updated Role',
                'permissions' => [
                    PermissionsEnum::ROLES_VIEW->value,
                    PermissionsEnum::ROLES_LIST->value,
                ],
            ]);

        expect($response->status())->toBe(Response::HTTP_OK)
            ->and($response->headers->get('content-type'))->toContain('application/json');

        expect($response->json('data'))->toBe([
            'uuid' => $this->role->uuid,
            'name' => 'Updated Role',
            'usersCount' => 0,
            'permissionsCount' => 2,
            'createdAt' => $this->role->created_at->toJSON(),
            'updatedAt' => $response->json('data.updatedAt'),
        ]);

        expect($response->json('data'))->not->toHaveKeys(['id', 'permissions', 'users']);

        expect(Role::count())->toBe(1);

        $foundRole = Role::first();
        expect($foundRole->id)->toBe($this->role->id)
            ->and($foundRole->uuid)->toBe($this->role->uuid)
            ->and($foundRole->name)->toBe('Updated Role')
            ->and($foundRole->guard_name)->toBe('web')
            ->and($foundRole->permissions->pluck('name')->toArray())->toMatchArray([
                PermissionsEnum::ROLES_VIEW->value,
                PermissionsEnum::ROLES_LIST->value,
            ])
            ->and($foundRole->users->count())->toBe(0);
    });

    it('can remove all permissions when updating a role', function () {
        $this->role->givePermissionTo([
            PermissionsEnum::ROLES_VIEW->value,
            PermissionsEnum::ROLES_LIST->value,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('roles.update', ['role' => $this->role->uuid]), [
                'name' => 'Updated Role',
                'permissions' => [],
            ]);

        expect($response->json('data.permissionsCount'))->toBe(0);

        expect(Role::count())->toBe(1);

        $foundRole = Role::first();
        expect($foundRole->permissions->count())->toBe(0);
    })->depends('it can update a role with permissions');

    it('returns 404 when updating a non-existing role', function () {
        $response = $this->actingAs($this->user)
            ->putJson(route('roles.update', ['role' => 'non-existing-uuid']), [
                'name' => 'Updated Role',
            ]);

        expect($response->status())->toBe(Response::HTTP_NOT_FOUND);
    });

    it('validate name field when updating a role', function (string|int|null $name, ?string $expectedError = null) {
        $role = Role::factory()->create();

        $response = $this->actingAs($this->user)
            ->putJson(route('roles.update', $role->uuid), ['name' => $name]);

        $expectedError
            ? expect($response->json('errors.name.0'))->toBe($expectedError)
            : expect($response->json('errors.name'))->toBeNull();
    })->depends('it can update a role with permissions')->with('name validation data');

    it('validate permissions field when updating a role', function (array|string|null $permissions, ?string $expectedError, string $key) {
        $role = Role::factory()->create();

        $response = $this->actingAs($this->user)
            ->putJson(route('roles.update', $role->uuid), ['permissions' => $permissions]);

        $expectedError
            ? expect($response->json('errors')[$key][0])->toBe($expectedError)
            : expect($response->json('errors.permissions'))->toBeNull();
    })->depends('it can update a role with permissions')->with('permissions validation data');
});

describe('delete', function () {

    beforeEach(function () {
        $this->role = Role::factory()->create();
    });

    it('can delete a role', function () {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('roles.destroy', ['role' => $this->role->uuid]));

        expect($response->status())->toBe(Response::HTTP_NO_CONTENT);

        expect(Role::count())->toBe(0);
    });

    it('returns 404 when deleting a non-existing role', function () {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('roles.destroy', ['role' => 'non-existing-uuid']));

        expect($response->status())->toBe(Response::HTTP_NOT_FOUND);
    });
});

describe('authorization', function () {
    beforeEach(function () {
        $this->role = Role::factory()->create();
        $this->unauthorizedUser = User::factory()->create();
    });

    it('prevents unauthorized users from searching roles', function () {
        $userWithoutPermission = User::factory()->create();

        $response = test()->actingAs($userWithoutPermission)
            ->getJson(route('roles.index', ['name' => 'Editor']));

        expect($response->status())->toBe(Response::HTTP_FORBIDDEN);
    });

    it('prevents unauthorized users from creating a new role', function () {
        $response = $this->actingAs($this->unauthorizedUser)
            ->postJson(route('roles.store'), [
                'name' => 'new-role',
                'permissions' => [
                    PermissionsEnum::ROLES_VIEW->value,
                    PermissionsEnum::ROLES_LIST->value,
                ],
            ]);

        expect($response->status())->toBe(Response::HTTP_FORBIDDEN);
    });

    it('prevents unauthorized users from retrieving a role', function () {
        $response = $this->actingAs($this->unauthorizedUser)
            ->getJson(route('roles.show', ['role' => $this->role->uuid]));

        expect($response->status())->toBe(Response::HTTP_FORBIDDEN);
    });

    it('prevents unauthorized users from updating a role', function () {
        $response = $this->actingAs($this->unauthorizedUser)
            ->putJson(route('roles.update', ['role' => $this->role->uuid]), [
                'name' => 'Updated Role',
            ]);

        expect($response->status())->toBe(Response::HTTP_FORBIDDEN);
    });

    it('prevents unauthorized users from deleting a role', function () {
        $response = $this->actingAs($this->unauthorizedUser)
            ->deleteJson(route('roles.destroy', ['role' => $this->role->uuid]));

        expect($response->status())->toBe(Response::HTTP_FORBIDDEN);
    });
});
