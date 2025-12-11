<?php

declare(strict_types=1);

namespace App\Actions\Permissions;

use App\Models\Permission;

final class ListPermissionsAction
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function handle(?string $search = null)
    {
        return Permission::query()
            ->when(
                $search,
                fn ($query) => $query->whereLikeInsensitive('name', $search)
            )->get();
    }
}
