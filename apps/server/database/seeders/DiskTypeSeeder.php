<?php

namespace Database\Seeders;

use App\Enums\Enums\DiskDriverEnum;
use App\Enums\Enums\DiskTypeEnum;
use App\Models\DiskType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiskTypeSeeder extends Seeder
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
                'driver' => DiskDriverEnum::LOCAL->value,
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
