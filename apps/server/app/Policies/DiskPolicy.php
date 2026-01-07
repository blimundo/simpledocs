<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Models\Disk;
use App\Models\User;

final class DiskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionsEnum::DISKS_LIST);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Disk $disk): bool
    {
        return $user->can(PermissionsEnum::DISKS_VIEW);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionsEnum::DISKS_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Disk $disk): bool
    {
        return $user->can(PermissionsEnum::DISKS_EDIT);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Disk $disk): bool
    {
        return $user->can(PermissionsEnum::DISKS_DELETE);
    }
}
