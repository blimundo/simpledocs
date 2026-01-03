<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Find a model by its UUID.
     */
    public static function findByUuid(string $uuid): ?self
    {
        return static::where('uuid', $uuid)->first();
    }

    /**
     * Get the route key name for the model.
     *
     * This method overrides the default route key name to use the 'uuid' field
     * instead of the default primary key.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Boot the trait and assign a UUID to the model upon creation.
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function (self $model) {
            $model->attributes['uuid'] = $model->uuid ?: Str::uuid()->toString();
        });
    }
}
