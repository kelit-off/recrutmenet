<?php

namespace Database\Factories;

use App\Enums\JobPostStatus;
use App\Models\JobPost;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobPost>
 */
class JobPostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sector_id' => Sector::factory(),
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraphs(3, true),
            'location' => fake()->city(),
            'contract_type' => fake()->randomElement(['CDI', 'CDD', 'Stage', 'Bénévolat']),
            'positions_count' => 2,
            'status' => JobPostStatus::Open,
            'opens_at' => now()->subDays(10),
            'closes_at' => now()->addMonth(),
        ];
    }

    public function closed(): static
    {
        return $this->state(['status' => JobPostStatus::Closed]);
    }

    public function draft(): static
    {
        return $this->state(['status' => JobPostStatus::Draft]);
    }
}
