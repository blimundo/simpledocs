<?php

declare(strict_types=1);

namespace App\Actions\Permissions\Roles;

use App\Models\Role;

final class DeleteRoleAction
{
    public function handle(Role $role): bool
    {
        return (bool) $role->delete();
    }
}
