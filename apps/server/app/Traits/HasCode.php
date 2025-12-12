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
}
