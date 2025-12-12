<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DiskDriverEnum;
use App\Enums\DiskTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiskType>
 */
final class DiskTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => DiskTypeEnum::LOCAL->value,
            'name' => 'Local Storage',
            'driver' => DiskDriverEnum::LOCAL->value,
            'fields' => [
                'root' => '/path/to/storage',
            ],
        ];
    }
}
