<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_post_id' => JobPost::factory(),
            'user_id' => User::factory()->member(),
            'motivation' => fake()->paragraph(),
            'experience' => fake()->paragraph(),
            'availability' => 'Soirs et week-ends',
            'status' => ApplicationStatus::Pending,
        ];
    }

    public function status(ApplicationStatus $status): static
    {
        return $this->state(['status' => $status]);
    }
}
