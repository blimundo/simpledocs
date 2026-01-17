<?php

declare(strict_types=1);

namespace App\Enums;

enum DiskTypeEnum: string
{
    case LOCAL = 'local';

    /**
     * Get the corresponding filesystem driver.
     */
    public function getDriver(): string
    {
        return match ($this) {
            self::LOCAL => 'local',
        };
    }
}
