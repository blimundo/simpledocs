<?php

declare(strict_types=1);

use App\Data\Permissions\Roles\SearchRolesData;

uses()->group('unit', 'data', 'permissions', 'roles');

describe('instantiation', function () {
    it('can be instantiated with valid data', function () {
        $data = new SearchRolesData(
            search: 'admin',
            page: 1,
            perPage: 10,
            sortBy: 'name',
            sortOrder: 'asc'
        );

        expect($data)->toBeInstanceOf(SearchRolesData::class)
            ->search->toBeString('admin')
            ->page->toBeInt(1)
            ->perPage->toBeInt(10)
            ->sortBy->toBeString('name')
            ->sortOrder->toBeString('asc');
    });

    it('uses default values when parameters are not provided', function () {
        $data = new SearchRolesData();

        expect($data)->toBeInstanceOf(SearchRolesData::class)
            ->search->toBeNull()
            ->page->toBeNull()
            ->perPage->toBeNull()
            ->sortBy->toBeString('name')
            ->sortOrder->toBeString('asc');
    });
});
