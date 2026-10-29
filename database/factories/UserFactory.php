<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        $username = fake()->unique()->userName();

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'discord_id' => (string) fake()->unique()->numberBetween(100000000000000000, 999999999999999999),
            'discord_username' => Str::lower($username),
            'avatar' => null,
            'remember_token' => Str::random(10),
        ];
    }

    public function withRole(Role $role): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole($role->value));
    }

    public function member(): static
    {
        return $this->withRole(Role::Member);
    }

    public function sectorManager(): static
    {
        return $this->withRole(Role::SectorManager);
    }

    public function direction(): static
    {
        return $this->withRole(Role::Direction);
    }

    public function hr(): static
    {
        return $this->withRole(Role::Hr);
    }

    public function admin(): static
    {
        return $this->withRole(Role::Admin);
    }
}
