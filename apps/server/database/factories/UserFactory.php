<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PermissionsEnum;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
final class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => self::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Assign permissions to the user after creation.
     *
     * @param  string|Permission|PermissionsEnum|array<Permission|string|PermissionsEnum>|Collection<int, Permission>  $permission
     */
    public function withPermission(string|Permission|PermissionsEnum|array|Collection $permission): self
    {
        return $this->afterCreating(
            fn (User $user) => $user->givePermissionTo($permission)
        );
    }
}
