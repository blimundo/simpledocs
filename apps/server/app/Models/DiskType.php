<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Fields\FieldCollectionData;
use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property-read string $code
 * @property-read string $name
 * @property-read string $driver
 * @property-read array<string, mixed> $fields
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Disk> $disks
 */
final class DiskType extends Model
{
    /** @use HasFactory<\Database\Factories\DiskTypeFactory> */
    use HasCode, HasFactory;

    /**
     * Disable timestamps for this model.
     */
    public $timestamps = false;

    /**
     * The relationships that should always be counted.
     */
    protected $withCount = ['disks'];

    /**
     * Get validation rules for the disk type fields.
     *
     * @return array<string, array<int, string>>
     */
    public function getValidationRules(): array
    {
        return FieldCollectionData::from(['fields' => $this->fields])
            ->asValidationRules();
    }

    /**
     * Get form representation of the disk type fields.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFormRepresentation(): array
    {
        return FieldCollectionData::from(['fields' => $this->fields])
            ->asForm();
    }

    /**
     * Get the disks associated with the disk type.
     *
     * @return HasMany<Disk, $this>
     */
    public function disks(): HasMany
    {
        return $this->hasMany(Disk::class, 'disk_type_id');
    }

    /**
     * Resolve the route binding for the model.
     *
     * This method overrides the default behavior to perform a case-insensitive
     * lookup based on the 'code' field.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function resolveRouteBinding($value, $field = null): Model
    {
        $field = $field ?? $this->getRouteKeyName();

        return $this->whereLikeInsensitive($field, $value)->firstOrFail();
    }

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
