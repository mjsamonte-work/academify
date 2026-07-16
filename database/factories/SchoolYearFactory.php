<?php

namespace Database\Factories;

use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolYear>
 */
class SchoolYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = fake()->numberBetween(2026, 2035);

        return [
            'name' => "{$year}-".($year + 1),
            'starts_at' => "{$year}-06-01",
            'ends_at' => ($year + 1).'-03-31',
            'is_active' => false,
            'status' => SchoolYear::STATUS_ACTIVE,
        ];
    }
}
