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

    // Disks/Disks
    case DISKS_CREATE = 'disks.create';
    case DISKS_DELETE = 'disks.delete';
    case DISKS_EDIT = 'disks.edit';
    case DISKS_LIST = 'disks.list';
    case DISKS_VIEW = 'disks.view';
}
