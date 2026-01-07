<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\DiskTypeEnum;
use App\Models\DiskType;
use Illuminate\Database\Seeder;

final class DiskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Local disk type
        DiskType::updateOrCreate(
            ['code' => DiskTypeEnum::LOCAL->value],
            [
                'name' => 'Local',
                'driver' => DiskTypeEnum::LOCAL->getDriver(),
                'fields' => [
                    [
                        'name' => 'root',
                        'type' => 'string',
                        'label' => 'Root Path',
                        'required' => true,
                        'max' => 500,
                    ],
                ],
            ]
        );
    }
}
