<?php

declare(strict_types=1);

use App\Data\Disks\Disks\UpdateDiskData;
use App\Models\DiskType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'data', 'disks');

beforeEach(function () {
    DiskType::factory()->create(['code' => 'local']);
});

describe('instantiation', function () {
    it('can be instantiated with valid data', function () {
        $data = new UpdateDiskData(
            name: 'My Disk',
            config: ['path' => '/mnt/disk1'],
            size: 100,
            used: 50,
        );

        expect($data)
            ->toBeInstanceOf(UpdateDiskData::class)
            ->name->toBe('My Disk')
            ->config->toBe(['path' => '/mnt/disk1'])
            ->size->toBe(100)
            ->used->toBe(50);
    });

    it('can be instantiated with default values', function () {
        $data = new UpdateDiskData();

        expect($data)
            ->name->toBeNull()
            ->config->toBeNull()
            ->used->toBeNull()
            ->size->toBeNull();
    });
});
