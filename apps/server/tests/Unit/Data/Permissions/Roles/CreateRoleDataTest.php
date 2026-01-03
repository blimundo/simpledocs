<?php

declare(strict_types=1);

use App\Data\Permissions\Roles\CreateRoleData;
use App\Enums\PermissionsEnum;

uses()->group('unit', 'data', 'permissions', 'roles');

describe('instantiation', function () {
    it('can be instantiated with valid data', function () {
        $data = new CreateRoleData(
            name: 'Editor',
            permissions: ['edit_articles', 'publish_articles']
        );

        expect($data)->toBeInstanceOf(CreateRoleData::class)
            ->name->toBeString('Editor')
            ->permissions->toBeArray()
            ->permissions->toEqual(['edit_articles', 'publish_articles']);
    });

    it('uses default values when parameters are not provided', function () {
        $data = new CreateRoleData(name: 'Editor');

        expect($data)->permissions->toBeArray()
            ->permissions->toEqual([]);
    });

    it('can accept PermissionsEnum in permissions array', function () {
        $data = new CreateRoleData(
            name: 'Editor',
            permissions: [
                PermissionsEnum::ROLES_CREATE,
                PermissionsEnum::ROLES_DELETE->value,
                'custom.permission',
            ]
        );

        expect($data)->permissions->toBeArray()
            ->permissions->toEqual([
                PermissionsEnum::ROLES_CREATE,
                PermissionsEnum::ROLES_DELETE->value,
                'custom.permission',
            ]);
    });
});
