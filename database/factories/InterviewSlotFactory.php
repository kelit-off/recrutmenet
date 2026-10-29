<?php

namespace Database\Factories;

use App\Enums\SlotSide;
use App\Enums\SlotStatus;
use App\Models\Interview;
use App\Models\InterviewSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InterviewSlot>
 */
class InterviewSlotFactory extends Factory
{
    public function definition(): array
    {
        return [
            'interview_id' => Interview::factory(),
            'proposed_by_side' => SlotSide::Direction,
            'starts_at' => now()->addDays(3)->setTime(14, 0),
            'duration_minutes' => 45,
            'status' => SlotStatus::Pending,
        ];
    }
}
