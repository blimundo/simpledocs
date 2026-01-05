<?php

declare(strict_types=1);

namespace App\Http\Resources\Disks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\DiskType
 */
final class DiskTypeListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'disksCount' => $this->whenCounted('disks'),
        ];
    }
}
