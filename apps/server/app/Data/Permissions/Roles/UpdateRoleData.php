<?php

declare(strict_types=1);

namespace App\Data\Permissions\Roles;

use App\Enums\PermissionsEnum;
use Spatie\LaravelData\Data;

final class UpdateRoleData extends Data
{
    /** @param array<string|PermissionsEnum> $permissions */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?array $permissions = null,
    ) {}
}
