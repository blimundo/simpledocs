<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionsEnum: string
{
    // Permissions/Roles
    case ROLES_CREATE = 'roles.create';
    case ROLES_DELETE = 'roles.delete';
    case ROLES_EDIT = 'roles.edit';
    case ROLES_LIST = 'roles.list';
    case ROLES_VIEW = 'roles.view';
}
