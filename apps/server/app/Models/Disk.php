<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int $id
 * @property-read int $disk_type_id
 * @property-read string $uuid
 * @property-read string $name
 * @property-read array<string, mixed> $config
 * @property-read int $size
 * @property-read int $used
 * @property-read \Illuminate\Support\Carbon|null $created_at
 * @property-read \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Support\Carbon|null $deleted_at
 * @property-read DiskType $type
 */
final class Disk extends Model
{
    /** @use HasFactory<\Database\Factories\DiskFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    /**
     * Get the disk type associated with the disk.
     *
     * @return BelongsTo<DiskType, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(DiskType::class, 'disk_type_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'config' => 'array',
        ];
    }
}
