<?php

declare(strict_types=1);

namespace App\Actions\Disks\Disks;

use App\Data\Disks\Disks\SearchDisksData;
use App\Models\Disk;

final class SearchDisksAction
{
    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, Disk>
     */
    public function handle(SearchDisksData $data)
    {
        return Disk::query()
            ->with('type')
            ->when(
                $data->type,
                fn ($query, $type) => $query->whereHas('type', fn ($q) => $q->where('code', $type))
            )
            ->when($data->name, fn ($query, $name) => $query->whereLikeInsensitive('name', $name))
            ->orderBy($data->sortBy, $data->sortOrder)
            ->paginate(page: $data->page, perPage: $data->perPage);
    }
}
