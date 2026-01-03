<?php

declare(strict_types=1);

namespace App\Actions\Permissions\Roles;

use App\Data\Permissions\Roles\CreateRoleData;
use App\Models\Role;

final class CreateRoleAction
{
    public function handle(CreateRoleData $data): Role
    {
        /** @var Role $role */
        $role = Role::create([
            'name' => $data->name,
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo($data->permissions);

        return $role;
    }
}
