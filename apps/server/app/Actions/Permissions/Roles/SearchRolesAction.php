<?php

declare(strict_types=1);

namespace App\Actions\Permissions\Roles;

use App\Data\Permissions\Roles\SearchRolesData;
use App\Models\Role;

final class SearchRolesAction
{
    /** @return \Illuminate\Pagination\LengthAwarePaginator<int, Role> */
    public function handle(SearchRolesData $data)
    {
        return Role::query()
            ->when(
                $data->search,
                fn ($query, $search) => $query->whereLikeInsensitive('name', $search)
            )
            ->orderBy($data->sortBy, $data->sortOrder)
            ->paginate(page: $data->page, perPage: $data->perPage);
    }
}
