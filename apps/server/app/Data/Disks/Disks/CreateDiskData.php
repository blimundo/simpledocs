<?php

declare(strict_types=1);

namespace App\Data\Disks\Disks;

use Spatie\LaravelData\Data;

final class CreateDiskData extends Data
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly array $config,
        public readonly int $size,
        public readonly int $used = 0,
    ) {}
}
