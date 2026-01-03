<?php

declare(strict_types=1);

namespace App\Data\Permissions\Roles;

use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

final class SearchRolesData extends Data
{
    public readonly string $sortBy;

    public function __construct(
        public readonly ?string $search = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
        string $sortBy = 'name',
        public readonly string $sortOrder = 'asc',
    ) {
        $this->sortBy = Str::snake($sortBy);
    }
}
