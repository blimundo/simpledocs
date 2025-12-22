<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

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
     * @param  string|array<string>  $permission
     */
    public function withPermission(string|array $permission): self
    {
        return $this->afterCreating(
            fn (Role $role) => $role->givePermissionTo($permission)
        );
    }
}
