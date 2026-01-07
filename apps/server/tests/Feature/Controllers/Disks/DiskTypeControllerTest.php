<?php

declare(strict_types=1);

use App\Enums\DiskTypeEnum;
use App\Enums\FieldTypeEnum;
use App\Models\Disk;
use App\Models\DiskType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class)->group('feature', 'http', 'controllers', 'disks');

beforeEach(function () {
    test()->user = User::factory()->create();
});

describe('GET /disk-types', function () {
    beforeEach(function () {
        DiskType::factory()->createMany([
            ['code' => 'local', 'name' => 'Local Storage'],
            ['code' => 's3', 'name' => 'Amazon S3'],
            ['code' => 'ftp', 'name' => 'FTP Storage'],
        ]);
    });

    it('returns JSON content type', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disk-types.index'));

        expect($response->status())->toBe(Response::HTTP_OK)
            ->and($response->headers->get('content-type'))->toContain('application/json');
    });

    it('returns all disk types in correct format', function () {
        Disk::factory()->createMany([
            ['disk_type_id' => 1],
            ['disk_type_id' => 2],
        ]);

        $response = actingAs(test()->user)
            ->getJson(route('disk-types.index'));

        expect($response->json())->not->toHaveKeys(['links', 'meta']);

        expect($response->json('data.0'))->toBe([
            'code' => 's3',
            'name' => 'Amazon S3',
            'disksCount' => 1,
        ]);
    });

    it('excludes internal fields from response', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disk-types.index'));

        expect($response->json('data.0'))
            ->not->toHaveKey('id')
            ->not->toHaveKey('driver')
            ->not->toHaveKey('fields');
    });

    it('sorts disk types by name in ascending order', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disk-types.index'));

        $names = collect($response->json('data'))->pluck('name')->toArray();

        expect($names)->toBe(['Amazon S3', 'FTP Storage', 'Local Storage']);
    });

    it('filters disk types by search query', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disk-types.index', ['search' => 'ftp']));

        expect($response->json('data'))
            ->toBeArray()
            ->toHaveCount(1)
            ->and($response->json('data.0.name'))->toBe('FTP Storage');
    });

    it('returns empty array when no disk types exist', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disk-types.index', ['search' => 'nonexistent']));

        expect($response->json('data'))
            ->toBeArray()
            ->toBeEmpty();
    });

    testAuthenticationAndAuthorization('GET', 'disk-types.index', withAuthorization: false);
});

describe('GET /disk-types/{disk_type}', function () {
    beforeEach(function () {
        DiskType::factory()->create([
            'code' => DiskTypeEnum::LOCAL->value,
            'name' => 'Local Storage',
            'driver' => DiskTypeEnum::LOCAL->getDriver(),
            'fields' => [
                [
                    'name' => 'path',
                    'type' => FieldTypeEnum::STRING->value,
                    'label' => 'Storage Path',
                    'max' => 500,
                ],
            ],
        ]);
    });

    it('returns JSON content type', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disk-types.show', 'local'));

        expect($response->status())->toBe(Response::HTTP_OK)
            ->and($response->headers->get('content-type'))->toContain('application/json');
    });

    it('returns disk type by code', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disk-types.show', 'local'));

        expect($response->json('data'))
            ->toBe([
                'code' => DiskTypeEnum::LOCAL->value,
                'name' => 'Local Storage',
                'disksCount' => 0,
                'widgets' => [
                    [
                        'name' => 'path',
                        'label' => 'Storage Path',
                        'widget' => 'text',
                        'rules' => [
                            'required' => true,
                            'maxlength' => 500,
                        ],
                    ],
                ],
            ]);
    });

    it('excludes internal fields from response', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disk-types.show', 'local'))
            ->assertOk();

        expect($response->json('data'))
            ->not->toHaveKey('driver')
            ->not->toHaveKey('id');
    });

    it('returns not found for nonexistent disk type', function () {
        $response = actingAs(test()->user)
            ->getJson(route('disk-types.show', 'nonexistent'));

        expect($response->status())->toBe(Response::HTTP_NOT_FOUND);
    });

    testAuthenticationAndAuthorization(
        'GET',
        'disk-types.show',
        fn () => ['disk_type' => DiskTypeEnum::LOCAL->value],
        false
    );
});
