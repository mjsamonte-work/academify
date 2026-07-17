<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\ClassSchedule;
use App\Models\GradingPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assessment>
 */
class AssessmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_schedule_id' => ClassSchedule::factory(),
            'grading_period_id' => GradingPeriod::factory(),
            'title' => fake()->sentence(3),
            'assessment_type' => fake()->randomElement(['quiz', 'activity', 'exam']),
            'max_score' => 100,
            'weight' => 0,
            'due_date' => now()->addWeek()->toDateString(),
            'status' => Assessment::STATUS_DRAFT,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
