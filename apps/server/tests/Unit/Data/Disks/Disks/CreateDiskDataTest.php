<?php

declare(strict_types=1);

use App\Data\Disks\Disks\CreateDiskData;
use App\Models\DiskType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'data', 'disks');

beforeEach(function () {
    DiskType::factory()->create(['code' => 'local']);
});

describe('instantiation', function () {
    it('can be instantiated with valid data', function () {
        $data = new CreateDiskData(
            name: 'My Disk',
            type: 'local',
            config: ['path' => '/mnt/disk1'],
            size: 100,
            used: 50,
        );

        expect($data)
            ->toBeInstanceOf(CreateDiskData::class)
            ->name->toBe('My Disk')
            ->type->toBe('local')
            ->config->toBe(['path' => '/mnt/disk1'])
            ->size->toBe(100)
            ->used->toBe(50);
    });

    it('can be instantiated with default values', function () {
        $data = new CreateDiskData(
            name: 'Default Disk',
            type: 'local',
            config: ['path' => '/mnt/disk1'],
            size: 100,
        );

        expect($data->used)->toBe(0);
    });
});
