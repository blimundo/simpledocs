<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @property-read int $id
 * @property-read string $uuid
 * @property-read string $name
 * @property-read string $guard_name
 * @property-read \Illuminate\Support\Carbon|null $created_at
 * @property-read \Illuminate\Support\Carbon|null $updated_at
 */
final class Role extends SpatieRole
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory, HasUuid;
}
