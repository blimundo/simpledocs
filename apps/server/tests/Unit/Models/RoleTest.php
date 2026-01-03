<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'models', 'permission');

describe('attributes', function () {
    it('has correct array keys and types', function () {
        $role = Role::factory()->create()->refresh();

        expect($role->toArray())
            ->toHaveKeys([
                'id',
                'uuid',
                'name',
                'guard_name',
                'created_at',
                'updated_at',
            ])
            ->not->toHaveKeys(['deleted_at']);

        expect($role)
            ->id->toBeInt()
            ->uuid->toBeUuid()
            ->name->toBeString()
            ->guard_name->toBeString()
            ->created_at->toBeInstanceOf(Carbon\CarbonImmutable::class)
            ->updated_at->toBeInstanceOf(Carbon\CarbonImmutable::class);
    });
});

describe('uuid', function () {

    it('generates uuid automatically on creation', function () {
        $role = Role::factory()->create(['uuid' => null]);

        expect($role->uuid)->toBeUuid();
    });

    it('can be retrieved by uuid', function () {
        $role = Role::factory()->create();

        $foundRole = Role::findByUuid($role->uuid);

        expect($foundRole)->not->toBeNull()
            ->and($foundRole->id)->toBe($role->id);

        $notFoundRole = Role::findByUuid('non-existing-uuid');

        expect($notFoundRole)->toBeNull();
    });
});

describe('permissions', function () {

    beforeEach(function () {
        Permission::factory(2)->sequence(
            ['name' => 'edit articles'],
            ['name' => 'delete articles']
        )->create();
    });

    it('can assign permissions', function () {
        $role = Role::factory()->create();

        $role->givePermissionTo('edit articles');

        expect($role->hasPermissionTo('edit articles'))->toBeTrue()
            ->and($role->hasPermissionTo('delete articles'))->toBeFalse();
    });

    it('can revoke permissions', function () {
        $role = Role::factory()->withPermission('edit articles')->create();

        expect($role->hasPermissionTo('edit articles'))->toBeTrue()
            ->and($role->hasPermissionTo('delete articles'))->toBeFalse();

        $role->revokePermissionTo('edit articles');

        expect($role->hasPermissionTo('edit articles'))->toBeFalse()
            ->and($role->hasPermissionTo('delete articles'))->toBeFalse();
    });
});
