<?php

declare(strict_types=1);

namespace App\Actions\Disks\Disks;

use App\Models\Disk;

final class DeleteDiskAction
{
    public function handle(Disk $disk): bool
    {
        return (bool) $disk->delete();
    }
}
