<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Interview;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Interview>
 */
class InterviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'location_or_link' => 'https://discord.gg/'.fake()->lexify('??????'),
        ];
    }
}
