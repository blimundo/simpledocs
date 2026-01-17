<?php

declare(strict_types=1);

use App\Enums\DiskTypeEnum;
use App\Enums\PermissionsEnum;
use App\Models\Disk;
use App\Models\DiskType;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class)->group('feature', 'http', 'controllers', 'disks');

beforeEach(function () {
    $permissions = Permission::factory()->createMany([
        ['name' => PermissionsEnum::DISKS_LIST],
        ['name' => PermissionsEnum::DISKS_CREATE],
        ['name' => PermissionsEnum::DISKS_VIEW],
        ['name' => PermissionsEnum::DISKS_EDIT],
        ['name' => PermissionsEnum::DISKS_DELETE],
    ]);

    test()->user = User::factory()->withPermission($permissions)->create();
    test()->diskType = DiskType::factory()->create([
        'code' => DiskTypeEnum::LOCAL->value,
        'fields' => [
            [
                'name' => 'root',
                'type' => 'string',
                'label' => 'Root Path',
                'required' => true,
                'max' => 500,
            ],
        ],
    ]);
});

describe('GET /disks', function () {
    beforeEach(function () {
        Disk::factory()
            ->state([
                'disk_type_id' => test()->diskType->id,
                'config' => ['path' => '/mnt/storage'],
                'size' => 1_000_000,
                'used' => 250_000,
            ])
            ->createMany([
                ['name' => 'Games'],
                ['name' => 'Musics'],
                ['name' => 'Documents'],
            ]);
    });

    it('returns JSON content type', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disks.index'));

        expect($response->status())->toBe(Response::HTTP_OK)
            ->and($response->headers->get('content-type'))->toContain('application/json');
    });

    it('returns all disks in correct format', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disks.index'));

        expect($response->json())->toHaveKeys(['links', 'meta']);

        expect($response->json('data'))->toHaveCount(3)
            ->and($response->json('data.0'))->toBe([
                'uuid' => Disk::orderBy('name')->first()->uuid,
                'name' => 'Documents',
                'type' => test()->diskType->code,
                'size' => 1_000_000,
                'used' => 250_000,
            ]);
    });

    it('excludes internal fields from response', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disks.index'));

        expect($response->json('data.0'))
            ->not->toHaveKey('id')
            ->not->toHaveKey('config');
    });

    it('sorts disks by name in ascending order', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disks.index'));

        $disks = collect($response->json('data'))->pluck('name')->toArray();

        expect($disks)->toBe(['Documents', 'Games', 'Musics']);
    });

    it('searches disks by name', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disks.index', ['name' => 'Games']));

        expect($response->json('data'))
            ->toBeArray()
            ->toHaveCount(1)
            ->and($response->json('data.0.name'))->toBe('Games');
    });

    it('searches disks by type', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disks.index', ['type' => 'local']));

        expect($response->json('data'))
            ->toBeArray()
            ->toHaveCount(3)
            ->and($response->json('data.0.name'))->toBe('Documents');
    });

    it('returns empty array when no disks exists', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disks.index', ['name' => 'nonexistent']));

        expect($response->json('data'))
            ->toBeArray()
            ->toBeEmpty();
    });

    testFormRequestValidations('GET', 'disks.index', [
        'name' => 'search name validation data',
        'type' => 'search type validation data',
    ]);

    testPaginationParameters('disks.index');

    testAuthenticationAndAuthorization('GET', 'disks.index');
});

describe('POST /disks', function () {
    it('creates a new disk and returns it in correct format', function () {
        $response = actingAs(test()->user)
            ->postJson(route('disks.store'), [
                'name' => 'Backups',
                'type' => 'local',
                'config' => ['path' => '/mnt/backups'],
                'size' => 5_000_000,
            ]);

        expect($response->status())->toBe(Response::HTTP_CREATED)
            ->and($response->headers->get('content-type'))->toContain('application/json');

        expect($response
            ->json('data.uuid'))->toBeString()->toBeUuid()
            ->and($response->json('data.name'))->toBe('Backups')
            ->and($response->json('data.type'))->toBe('local')
            ->and($response->json('data.config'))->toBe(['path' => '/mnt/backups'])
            ->and($response->json('data.size'))->toBe(5_000_000)
            ->and($response->json('data.used'))->toBe(0)
            ->and($response->json('data.createdAt'))->toBeString()
            ->and($response->json('data.updatedAt'))->toBeString();

        expect($response->json('data'))->not->toHaveKey('id');

        expect(Disk::count())->toBe(1);

        expect(Disk::first())
            ->uuid->toBe($response->json('data.uuid'))
            ->name->toBe('Backups')
            ->type->code->toBe('local')
            ->config->toBe(['path' => '/mnt/backups'])
            ->size->toBe(5_000_000)
            ->used->toBe(0)
            ->created_at->toJson()->toBe($response->json('data.createdAt'))
            ->updated_at->toJson()->toBe($response->json('data.updatedAt'))
            ->deleted_at->toBeNull();
    });

    testFormRequestValidations('POST', 'disks.store', [
        'name' => 'name validation data',
        'type' => 'type validation data',
        'config' => 'config validation data',
        'size' => 'size validation data',
        'used' => 'used validation data',
    ]);

    testAuthenticationAndAuthorization('POST', 'disks.store');
});

describe('GET /disks/{disk}', function () {
    beforeEach(function () {
        test()->disk = Disk::factory()->create([
            'name' => 'Media',
            'disk_type_id' => test()->diskType->id,
            'config' => ['path' => '/mnt/storage'],
            'size' => 2_000_000,
            'used' => 500_000,
        ]);
    });

    it('returns the specified disk in correct format', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disks.show', ['disk' => test()->disk->uuid]));

        expect($response->status())->toBe(Response::HTTP_OK)
            ->and($response->headers->get('content-type'))->toContain('application/json');

        expect($response->json('data.uuid'))->toBe(test()->disk->uuid)
            ->and($response->json('data.name'))->toBe('Media')
            ->and($response->json('data.type'))->toBe(test()->diskType->code)
            ->and($response->json('data.config'))->toBe(['path' => '/mnt/storage'])
            ->and($response->json('data.size'))->toBe(2_000_000)
            ->and($response->json('data.used'))->toBe(500_000)
            ->and($response->json('data.createdAt'))->toBeString()
            ->and($response->json('data.updatedAt'))->toBeString()
            ->and($response->json('data.deletedAt'))->toBeNull();

        expect($response->json('data'))->not->toHaveKey('id');
    });

    it('returns 404 when the specified disk does not exist', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disks.show', ['disk' => 'nonexistent-uuid']));

        expect($response->status())->toBe(Response::HTTP_NOT_FOUND);
    });

    testAuthenticationAndAuthorization('GET', 'disks.show', fn () => ['disk' => test()->disk->uuid]);
});

describe('PUT /disks/{disk}', function () {
    beforeEach(
        fn () => test()->disk = Disk::factory()->create(['disk_type_id' => test()->diskType->id])
    );

    it('updates the specified disk and returns it in correct format', function () {
        $response = actingAs(test()->user)
            ->putJson(route('disks.update', ['disk' => test()->disk->uuid]), [
                'name' => 'Updated Disk Name',
                'config' => ['path' => '/mnt/updated_storage'],
                'size' => 3_000_000,
                'used' => 1_000_000,
            ]);

        expect($response->status())->toBe(Response::HTTP_OK)
            ->and($response->headers->get('content-type'))->toContain('application/json');

        expect($response->json('data.uuid'))->toBe(test()->disk->uuid)
            ->and($response->json('data.name'))->toBe('Updated Disk Name')
            ->and($response->json('data.type'))->toBe(test()->diskType->code)
            ->and($response->json('data.config'))->toBe(['path' => '/mnt/updated_storage'])
            ->and($response->json('data.size'))->toBe(3_000_000)
            ->and($response->json('data.used'))->toBe(1_000_000)
            ->and($response->json('data.createdAt'))->toBeString()
            ->and($response->json('data.updatedAt'))->toBeString()
            ->and($response->json('data.deletedAt'))->toBeNull();

        expect($response->json('data'))->not->toHaveKey('id');

        expect(Disk::count())->toBe(1);

        expect(Disk::first())
            ->uuid->toBe(test()->disk->uuid)
            ->name->toBe('Updated Disk Name')
            ->type->code->toBe(test()->diskType->code)
            ->config->toBe(['path' => '/mnt/updated_storage'])
            ->size->toBe(3_000_000)
            ->used->toBe(1_000_000);
    });

    it('partially updates the specified disk and returns it in correct format', function () {
        $response = actingAs(test()->user)
            ->putJson(route('disks.update', ['disk' => test()->disk->uuid]), [
                'name' => 'Partially Updated Disk Name',
            ]);

        expect($response->status())->toBe(Response::HTTP_OK)
            ->and($response->headers->get('content-type'))->toContain('application/json');

        expect($response->json('data.uuid'))->toBe(test()->disk->uuid)
            ->and($response->json('data.name'))->toBe('Partially Updated Disk Name')
            ->and($response->json('data.type'))->toBe(test()->diskType->code)
            ->and($response->json('data.config'))->toBe(test()->disk->config)
            ->and($response->json('data.size'))->toBe(test()->disk->size)
            ->and($response->json('data.used'))->toBe(test()->disk->used)
            ->and($response->json('data.createdAt'))->toBeString()
            ->and($response->json('data.updatedAt'))->toBeString()
            ->and($response->json('data.deletedAt'))->toBeNull();

        expect(Disk::first())
            ->uuid->toBe(test()->disk->uuid)
            ->name->toBe('Partially Updated Disk Name');
    })->depends('it updates the specified disk and returns it in correct format');

    it('returns 404 when the specified disk does not exist', function () {
        $response = actingAs(test()->user)
            ->putJson(route('disks.update', ['disk' => 'nonexistent-uuid']), []);

        expect($response->status())->toBe(Response::HTTP_NOT_FOUND);
    });

    testFormRequestValidations('PUT', 'disks.update', [
        'name' => 'name validation data',
        'config' => 'config validation data',
        'size' => 'size validation data',
        'used' => 'used validation data',
    ], fn () => ['disk' => test()->disk->uuid]);

    testAuthenticationAndAuthorization('PUT', 'disks.update', fn () => ['disk' => test()->disk->uuid]);
});

describe('DELETE /disks/{disk}', function () {
    beforeEach(fn () => test()->disk = Disk::factory()->create());

    it('deletes the specified disk', function () {
        $response = actingAs(test()->user)
            ->deleteJson(route('disks.destroy', ['disk' => test()->disk->uuid]));

        expect($response->status())->toBe(Response::HTTP_NO_CONTENT);

        expect(Disk::count())->toBe(0);
    });

    it('returns 404 when the specified disk does not exist', function () {
        $response = actingAs(test()->user)
            ->deleteJson(route('disks.destroy', ['disk' => 'nonexistent-uuid']));

        expect($response->status())->toBe(Response::HTTP_NOT_FOUND);
    });

    testAuthenticationAndAuthorization('DELETE', 'disks.destroy', fn () => ['disk' => test()->disk->uuid]);
});
