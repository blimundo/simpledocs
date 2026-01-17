<?php

declare(strict_types=1);

namespace App\Data\Disks\Disks;

use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

final class SearchDisksData extends Data
{
    public readonly string $sortBy;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $type = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
        string $sortBy = 'name',
        public readonly string $sortOrder = 'asc',
    ) {
        $this->sortBy = Str::snake($sortBy);
    }
}
