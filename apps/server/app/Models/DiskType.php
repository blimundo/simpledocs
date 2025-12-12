<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property-read string $code
 * @property-read string $name
 * @property-read string $driver
 * @property-read array<string, mixed> $fields
 */
final class DiskType extends Model
{
    /** @use HasFactory<\Database\Factories\DiskTypeFactory> */
    use HasCode, HasFactory;

    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fields' => 'array',
        ];
    }
}
