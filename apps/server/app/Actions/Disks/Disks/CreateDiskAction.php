<?php

declare(strict_types=1);

namespace App\Actions\Disks\Disks;

use App\Data\Disks\Disks\CreateDiskData;
use App\Models\Disk;
use App\Models\DiskType;

final class CreateDiskAction
{
    public function handle(CreateDiskData $data): Disk
    {
        $diskType = DiskType::findByCodeOrFail($data->type);

        return Disk::create([
            'name' => $data->name,
            'disk_type_id' => $diskType->id,
            'config' => $data->config,
            'used' => $data->used,
            'size' => $data->size,
        ]);
    }
}
