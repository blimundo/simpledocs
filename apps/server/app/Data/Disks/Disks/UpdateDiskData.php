<?php

declare(strict_types=1);

namespace App\Data\Disks\Disks;

use Spatie\LaravelData\Data;

final class UpdateDiskData extends Data
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?array $config = null,
        public readonly ?int $size = null,
        public readonly ?int $used = null,
    ) {}
}
