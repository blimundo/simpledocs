<?php

declare(strict_types=1);

use App\Actions\Disks\Disks\CreateDiskAction;
use App\Data\Disks\Disks\CreateDiskData;
use App\Models\Disk;
use App\Models\DiskType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'actions', 'disks');

beforeEach(function () {
    DiskType::factory()->create(['code' => 'local']);

    $this->action = resolve(CreateDiskAction::class);
});

it('creates a disk with valid data', function () {
    $data = new CreateDiskData(
        name: 'My Disk',
        type: 'local',
        config: ['path' => '/var/www/storage'],
        used: 0,
        size: 1000,
    );

    $disk = $this->action->handle($data);

    expect($disk)
        ->toBeInstanceOf(Disk::class)
        ->and($disk)
        ->name->toBe('My Disk')
        ->type->code->toBe('local')
        ->config->toBe(['path' => '/var/www/storage'])
        ->used->toBe(0)
        ->size->toBe(1000);
});

it('throws an exception when disk type does not exist', function () {
    $data = new CreateDiskData(
        name: 'Invalid Disk',
        type: 'nonexistent',
        config: [],
        used: 0,
        size: 500,
    );

    $this->action->handle($data);
})->throws(Illuminate\Database\Eloquent\ModelNotFoundException::class);
