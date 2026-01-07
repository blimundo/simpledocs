<?php

declare(strict_types=1);

dataset('pagination page validation data', [
    'minimum' => [0, fn () => __('validation.min.numeric', ['attribute' => 'page', 'min' => 1])],
    'integer' => ['abc', fn () => __('validation.integer', ['attribute' => 'page'])],
]);

dataset('pagination per page validation data', [
    'minimum' => [0, fn () => __('validation.min.numeric', ['attribute' => 'per page', 'min' => 1])],
    'maximum' => [51, fn () => __('validation.max.numeric', ['attribute' => 'per page', 'max' => 50])],
    'integer' => ['abc', fn () => __('validation.integer', ['attribute' => 'per page'])],
]);

dataset('pagination sort order validation data', [
    'invalid' => ['invalid', fn () => __('validation.in', ['attribute' => 'sort order'])],
    'ascending' => ['asc', null],
    'descending' => ['desc', null],
]);
