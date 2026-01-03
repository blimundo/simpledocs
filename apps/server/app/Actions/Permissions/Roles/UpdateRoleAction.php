<?php

declare(strict_types=1);

namespace App\Actions\Permissions\Roles;

use App\Data\Permissions\Roles\UpdateRoleData;
use App\Models\Role;

final class UpdateRoleAction
{
    public function handle(Role $role, UpdateRoleData $data): Role
    {
        $role->update(['name' => $data->name]);

        if ($data->permissions !== null) {
            $role->syncPermissions($data->permissions);
        }

        return $role;
    }
}
