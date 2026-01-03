<?php

declare(strict_types=1);

namespace App\Data\Permissions\Roles;

use Spatie\LaravelData\Data;

final class SearchRolesData extends Data
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
        public readonly string $sortBy = 'name',
        public readonly string $sortOrder = 'asc',
    ) {}
}
