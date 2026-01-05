<?php

declare(strict_types=1);

namespace App\Actions\Disks\Disks;

use App\Data\Disks\Disks\UpdateDiskData;
use App\Models\Disk;

final class UpdateDiskAction
{
    public function handle(Disk $disk, UpdateDiskData $data): Disk
    {
        $disk->update([
            'name' => $data->name ?? $disk->name,
            'config' => $data->config ?? $disk->config,
            'used' => $data->used ?? $disk->used,
            'size' => $data->size ?? $disk->size,
        ]);

        return $disk;
    }
}
