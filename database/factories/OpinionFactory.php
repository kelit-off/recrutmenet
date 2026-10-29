<?php

namespace Database\Factories;

use App\Enums\OpinionType;
use App\Enums\OpinionValue;
use App\Models\Application;
use App\Models\Opinion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Opinion>
 */
class OpinionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'user_id' => User::factory(),
            'type' => OpinionType::First,
            'value' => fake()->randomElement(OpinionValue::cases()),
            'comment' => fake()->sentence(),
        ];
    }
}
