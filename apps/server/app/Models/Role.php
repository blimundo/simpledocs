<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    /**
     * The relationships that should always be counted.
     */
    protected $withCount = [
        'permissions',
        'users',
    ];

    /**
     * Get all of the users that are assigned this role.
     *
     * @override
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        /** @var BelongsToMany<User, $this> */
        return $this->morphedByMany(
            User::class,
            'model',
            'model_has_roles',
            'role_id',
            'model_id'
        );
    }
}
