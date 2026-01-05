<?php

declare(strict_types=1);

use App\Data\Disks\Disks\SearchDisksData;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'data', 'disks');

describe('instantiation', function () {
    it('can be instantiated with valid data', function () {
        $data = new SearchDisksData(
            name: 'My Disk',
            type: 'local',
            page: 1,
            perPage: 10,
            sortBy: 'name',
            sortOrder: 'asc'
        );

        expect($data)
            ->toBeInstanceOf(SearchDisksData::class)
            ->name->toBe('My Disk')
            ->type->toBe('local')
            ->page->toBe(1)
            ->perPage->toBe(10)
            ->sortBy->toBe('name')
            ->sortOrder->toBe('asc');
    });

    it('can be instantiated with default values', function () {
        $data = new SearchDisksData();

        expect($data)
            ->name->toBeNull()
            ->type->toBeNull()
            ->page->toBeNull()
            ->perPage->toBeNull()
            ->sortBy->toBe('name')
            ->sortOrder->toBe('asc');
    });
});
