<?php

declare(strict_types=1);

namespace App\Traits;

trait HasCode
{
    /**
     * Find a model by its code.
     */
    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }

    /**
     * Find a model by its code or fail.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByCodeOrFail(string $code): self
    {
        return static::where('code', $code)->firstOrFail();
    }

    /**
     * Get the route key name for the model.
     *
     * This method overrides the default route key name to use the 'code' field
     * instead of the default primary key.
     */
    public function getRouteKeyName(): string
    {
        return 'code';
    }
}
