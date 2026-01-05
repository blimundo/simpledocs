<?php

declare(strict_types=1);

namespace App\Actions\Disks\DiskTypes;

use App\Models\DiskType;

final class ListDiskTypesAction
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, DiskType>
     */
    public function handle(?string $search = null)
    {
        return DiskType::query()
            ->when($search, fn ($query) => $query->where(
                fn ($q) => $q
                    ->whereLikeInsensitive('name', $search)
                    ->orWhere(fn ($subQ) => $subQ->whereLikeInsensitive('code', $search))
            ))
            ->orderBy('name')
            ->get();
    }
}
