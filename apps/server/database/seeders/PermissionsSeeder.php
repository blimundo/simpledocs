<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PermissionsEnum;
use App\Models\Permission;
use Illuminate\Database\Seeder;

final class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (PermissionsEnum::cases() as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission->value],
                ['guard_name' => 'web']
            );
        }
    }
}
