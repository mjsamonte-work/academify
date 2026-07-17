<?php

namespace Database\Factories;

use App\Models\GradingPeriod;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradingPeriod>
 */
class GradingPeriodFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_year_id' => SchoolYear::factory(),
            'name' => fake()->unique()->randomElement(['First Quarter', 'Second Quarter', 'Midterm', 'Final']),
            'starts_at' => now()->toDateString(),
            'ends_at' => now()->addMonth()->toDateString(),
            'sort_order' => 1,
            'status' => GradingPeriod::STATUS_ACTIVE,
        ];
    }
}
