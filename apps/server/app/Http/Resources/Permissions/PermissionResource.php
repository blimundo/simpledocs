<?php

declare(strict_types=1);

namespace App\Http\Resources\Permissions;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Permission
 */
final class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @phpstan-ignore method.childReturnType
     */
    public function toArray(Request $request): string
    {
        return $this->name;
    }
}
