<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $guard_name
 * @property-read \Illuminate\Support\Carbon|null $created_at
 * @property-read \Illuminate\Support\Carbon|null $updated_at
 */
final class Permission extends SpatiePermission
{
    /** @use HasFactory<\Database\Factories\PermissionFactory> */
    use HasFactory;
}
