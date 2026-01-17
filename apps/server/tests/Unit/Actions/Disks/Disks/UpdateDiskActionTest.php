<?php

declare(strict_types=1);

use App\Actions\Disks\Disks\UpdateDiskAction;
use App\Data\Disks\Disks\UpdateDiskData;
use App\Models\Disk;
use App\Models\DiskType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'actions', 'disks');

beforeEach(function () {
    $diskType = DiskType::factory()->create(['code' => 'local']);

    $this->disk = Disk::factory()->create(['disk_type_id' => $diskType->id]);
    $this->action = resolve(UpdateDiskAction::class);
});

it('updates an existing disk with valid data', function () {
    $data = new UpdateDiskData(
        name: 'Updated Disk Name',
        config: ['path' => '/new/path/to/storage'],
        used: 500,
        size: 2000,
    );

    $updatedDisk = $this->action->handle($this->disk, $data);

    expect($updatedDisk)->toBeInstanceOf(Disk::class)
        ->and($updatedDisk)
        ->id->toBe($this->disk->id)
        ->name->toBe('Updated Disk Name')
        ->config->toBe(['path' => '/new/path/to/storage'])
        ->used->toBe(500)
        ->size->toBe(2000);
});

it('does not change disk type when updating', function () {
    $originalTypeId = $this->disk->disk_type_id;

    $data = new UpdateDiskData(
        name: 'Another Update',
        config: ['path' => '/another/path'],
        used: 300,
        size: 1500,
    );

    $updatedDisk = $this->action->handle($this->disk, $data);

    expect($updatedDisk->disk_type_id)->toBe($originalTypeId);
});

it('updates only provided fields', function () {
    $updatedDisk = $this->action->handle($this->disk, new UpdateDiskData());

    expect($updatedDisk)
        ->id->toBe($this->disk->id)
        ->name->toBe($this->disk->name)
        ->config->toBe($this->disk->config)
        ->used->toBe($this->disk->used)
        ->size->toBe($this->disk->size);
});
