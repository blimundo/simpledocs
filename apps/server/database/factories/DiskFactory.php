<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DiskType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Disk>
 */
final class DiskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'disk_type_id' => DiskType::factory(),
            'uuid' => $this->faker->uuid(),
            'name' => $this->faker->unique()->words(3, true),
            'config' => [],
            'size' => $this->faker->numberBetween(100_000, 1_000_000_000),
            'used' => $this->faker->numberBetween(0, 100_000),
        ];
    }
}
