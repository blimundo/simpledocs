<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DiskTypeEnum;
use App\Enums\FieldTypeEnum;
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
            'code' => $this->faker->unique()->words(2, true),
            'name' => $this->faker->words(3, true),
            'driver' => DiskTypeEnum::LOCAL->getDriver(),
            'fields' => [
                [
                    'name' => 'path',
                    'type' => FieldTypeEnum::STRING->value,
                    'label' => 'Storage Path',
                    'max' => 500,
                ],
            ],
        ];
    }
}
