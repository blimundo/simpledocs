<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PermissionsEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
final class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->jobTitle(),
            'guard_name' => 'web',
        ];
    }

    /**
     * Assign permissions to the role after creation.
     *
     * @param  string|Permission|PermissionsEnum|array<Permission|string|PermissionsEnum>|Collection<int,Permission>  $permission
     */
    public function withPermission(string|Permission|PermissionsEnum|array|Collection $permission): self
    {
        return $this->afterCreating(
            fn (Role $role) => $role->givePermissionTo($permission)
        );
    }
}
