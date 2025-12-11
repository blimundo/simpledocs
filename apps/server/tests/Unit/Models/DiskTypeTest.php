<?php

use App\Models\DiskType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'models', 'disks');

it('can be converted to an array', function () {
    $diskType = DiskType::factory()->create()->refresh();

    expect(array_keys($diskType->toArray()))
        ->toBe([
            'id',
            'code',
            'name',
            'driver',
            'fields',
        ]);
});

it('has fields cast to array', function () {
    $diskType = DiskType::factory()->create([
        'fields' => [
            'key1' => 'value1',
            'key2' => 'value2',
        ],
    ])->refresh();

    expect($diskType->fields)
        ->toBeArray()
        ->toHaveCount(2)
        ->toMatchArray([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);
});

it('can be retrieved by code', function () {
    $diskType = DiskType::factory()->create();

    $foundDiskType = DiskType::findByCode($diskType->code);

    expect($foundDiskType)->not->toBeNull()
        ->and($foundDiskType->id)->toBe($diskType->id);

    $notFoundDiskType = DiskType::findByCode('non-existing-code');

    expect($notFoundDiskType)->toBeNull();
});
