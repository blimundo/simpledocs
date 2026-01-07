<?php

declare(strict_types=1);

namespace App\Http\Resources\Disks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Disk
 */
final class DiskListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'type' => $this->type->code,
            'size' => $this->size,
            'used' => $this->used,
        ];
    }
}
