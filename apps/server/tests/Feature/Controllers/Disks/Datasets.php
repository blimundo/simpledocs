<?php

declare(strict_types=1);

dataset('search name validation data', [
    'empty' => ['', null],
    'null' => [null, null],
    'exactly 100 characters' => [str_repeat('a', 100), null],
    'more than 100 characters' => [
        str_repeat('a', 101),
        fn () => __('validation.max.string', ['attribute' => 'name', 'max' => 100]),
    ],
]);

dataset('search type validation data', [
    'empty' => ['', null],
    'null' => [null, null],
    'invalid type' => ['invalid_type', fn () => __('validation.enum', ['attribute' => 'type'])],
    'valid type' => ['local', null],
]);

dataset('name validation data', [
    'null' => [null, fn () => __('validation.required', ['attribute' => 'name'])],
    'empty' => ['', fn () => __('validation.required', ['attribute' => 'name'])],
    'not a string' => [123, fn () => __('validation.string', ['attribute' => 'name'])],
    'less than 3 characters' => ['ab', fn () => __('validation.min.string', ['attribute' => 'name', 'min' => 3])],
    'exactly 3 characters' => ['abc', null],
    'exactly 100 characters' => [str_repeat('a', 100), null],
    'more than 100 characters' => [
        str_repeat('a', 101),
        fn () => __('validation.max.string', ['attribute' => 'name', 'max' => 100]),
    ],
]);

dataset('type validation data', [
    'null' => [null, fn () => __('validation.required', ['attribute' => 'type'])],
    'empty' => ['', fn () => __('validation.required', ['attribute' => 'type'])],
    'invalid type' => ['invalid_type', fn () => __('validation.enum', ['attribute' => 'type'])],
    'valid type' => ['local', null],
]);

dataset('config validation data', [
    'not an array' => ['not_an_array', fn () => __('validation.array', ['attribute' => 'config'])],
    'valid config' => [['path' => '/mnt/data'], null],
]);

dataset('size validation data', [
    'null' => [null, fn () => __('validation.required', ['attribute' => 'size'])],
    'empty' => ['', fn () => __('validation.required', ['attribute' => 'size'])],
    'not a number' => ['not_a_number', fn () => __('validation.numeric', ['attribute' => 'size'])],
    'negative number' => [-10, fn () => __('validation.min.numeric', ['attribute' => 'size', 'min' => 1])],
    'zero' => [0, fn () => __('validation.min.numeric', ['attribute' => 'size', 'min' => 1])],
    'positive number' => [1024, null],
]);

dataset('used validation data', [
    'null' => [null, null],
    'empty' => ['', null],
    'not a number' => ['not_a_number', fn () => __('validation.numeric', ['attribute' => 'used'])],
    'negative number' => [-10, fn () => __('validation.min.numeric', ['attribute' => 'used', 'min' => 0])],
    'zero' => [['used' => 0, 'size' => 1000], null],
    'positive number' => [['used' => 512, 'size' => 1000], null],
    'used less than size' => [['used' => 500, 'size' => 1000], null],
    'used equal to size' => [['used' => 1000, 'size' => 1000], null],
    'used greater than size' => [['used' => 1500, 'size' => 1000], fn () => __('validation.lte.numeric', ['attribute' => 'used', 'value' => 1000])],
]);
