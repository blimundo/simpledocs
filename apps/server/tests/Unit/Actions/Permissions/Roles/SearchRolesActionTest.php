<?php

declare(strict_types=1);

use App\Actions\Permissions\Roles\SearchRolesAction;
use App\Data\Permissions\Roles\SearchRolesData;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'actions', 'permissions', 'roles');

beforeEach(function () {
    $this->action = resolve(SearchRolesAction::class);
});

describe('filtering', function () {
    it('returns empty collection when no roles exist', function () {
        $result = $this->action->handle(
            new SearchRolesData()
        );

        expect($result)->toBeEmpty();
    });

    it('returns all roles when no filters are applied', function () {
        Role::factory()->createMany([
            ['name' => 'Admin'],
            ['name' => 'Editor'],
            ['name' => 'Viewer'],
        ]);

        $result = $this->action->handle(
            new SearchRolesData()
        );

        expect($result->count())->toBe(3)
            ->and($result->pluck('name'))->toContain('Admin')
            ->and($result->pluck('name'))->toContain('Editor')
            ->and($result->pluck('name'))->toContain('Viewer');
    });

    it('returns roles matching specific criteria', function () {
        Role::factory()->createMany([
            ['name' => 'Admin'],
            ['name' => 'Editor'],
            ['name' => 'Viewer'],
        ]);

        $result = $this->action->handle(
            new SearchRolesData(
                search: 'ITO'
            )
        );

        expect($result->count())->toBe(1)
            ->and($result->first()->name)->toBe('Editor');
    });
});

describe('pagination', function () {
    beforeEach(function () {
        Role::factory()->createMany([
            ['name' => 'Admin'],
            ['name' => 'Editor'],
            ['name' => 'Viewer'],
            ['name' => 'Contributor'],
            ['name' => 'Moderator'],
        ]);
    });

    it('returns paginated results', function () {
        $result = $this->action->handle(
            new SearchRolesData()
        );

        expect($result)->toBeInstanceOf(Illuminate\Pagination\LengthAwarePaginator::class)
            ->and($result->total())->toBe(5)
            ->and($result->perPage())->toBe(15)
            ->and($result->currentPage())->toBe(1)
            ->and($result->count())->toBe(5)
            ->and($result->first())->toBeInstanceOf(Role::class);
    });

    it('can change pagination parameters', function () {
        $result = $this->action->handle(
            new SearchRolesData(page: 1, perPage: 2)
        );

        expect($result->total())->toBe(5)
            ->and($result->perPage())->toBe(2)
            ->and($result->currentPage())->toBe(1)
            ->and($result->count())->toBe(2);

        $result = $this->action->handle(
            new SearchRolesData(page: 2, perPage: 3)
        );

        expect($result->perPage())->toBe(3)
            ->and($result->currentPage())->toBe(2)
            ->and($result->count())->toBe(2);
    });
});

describe('sorting', function () {
    beforeEach(function () {
        Role::factory()->createMany([
            ['name' => 'Viewer'],
            ['name' => 'Admin'],
            ['name' => 'Editor'],
        ]);
    });

    it('sorts roles by name ascending by default', function () {
        $result = $this->action->handle(
            new SearchRolesData()
        );

        expect($result->pluck('name')->toArray())->toBe([
            'Admin',
            'Editor',
            'Viewer',
        ]);
    });

    it('sorts roles by name descending when specified', function () {
        $result = $this->action->handle(
            new SearchRolesData(
                sortBy: 'name',
                sortOrder: 'desc'
            )
        );

        expect($result->pluck('name')->toArray())->toBe([
            'Viewer',
            'Editor',
            'Admin',
        ]);
    });

    it('sorts roles by created_at ascending when specified', function () {
        $result = $this->action->handle(
            new SearchRolesData(
                sortBy: 'created_at',
                sortOrder: 'asc'
            )
        );

        expect($result->pluck('name')->toArray())->toBe([
            'Viewer',
            'Admin',
            'Editor',
        ]);
    });
});
