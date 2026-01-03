<?php

declare(strict_types=1);

use App\Enums\PermissionsEnum;
use App\Models\Role;

dataset('name validation data', [
    'valid' => ['Valid Role Name', null],
    'empty' => ['', fn () => __('validation.required', ['attribute' => 'name'])],
    'null' => [null, fn () => __('validation.required', ['attribute' => 'name'])],
    'not a string' => [12345, fn () => __('validation.string', ['attribute' => 'name'])],
    'less than 3 characters' => ['ab', fn () => __('validation.min.string', ['attribute' => 'name', 'min' => 3])],
    'exactly 3 characters' => ['abc', null],
    'exactly 100 characters' => [str_repeat('a', 100), null],
    'more than 100 characters' => [
        str_repeat('a', 101),
        fn () => __('validation.max.string', ['attribute' => 'name', 'max' => 100]),
    ],
    'unique' => [
        fn () => Role::factory()->create()->name,
        fn () => __('validation.unique', ['attribute' => 'name']),
    ],
]);

dataset('permissions validation data', [
    'valid' => [
        [PermissionsEnum::ROLES_VIEW->value, PermissionsEnum::ROLES_LIST->value],
        null,
        'permissions',
    ],
    'null' => [
        null,
        fn () => __('validation.array', ['attribute' => 'permissions']),
        'permissions',
    ],
    'not an array' => [
        'not-an-array',
        fn () => __('validation.array', ['attribute' => 'permissions']),
        'permissions',
    ],
    'array with non-string values' => [
        [123, true, null],
        fn () => __('validation.string', ['attribute' => 'permissions.0']),
        'permissions.0',
    ],
    'array with invalid permission names' => [
        ['invalid-permission'],
        fn () => __('validation.exists', ['attribute' => 'permissions.0']),
        'permissions.0',
    ],
]);

dataset('search name validation data', [
    'empty' => ['', null],
    'null' => [null, null],
    'exactly 100 characters' => [str_repeat('a', 100), null],
    'more than 100 characters' => [
        str_repeat('a', 101),
        fn () => __('validation.max.string', ['attribute' => 'search', 'max' => 100]),
    ],
]);
