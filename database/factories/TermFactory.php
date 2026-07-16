<?php

namespace Database\Factories;

use App\Models\SchoolYear;
use App\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Term>
 */
class TermFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_year_id' => SchoolYear::factory(),
            'name' => fake()->randomElement(['First Term', 'Second Term', 'Third Term']),
            'starts_at' => now()->startOfMonth(),
            'ends_at' => now()->startOfMonth()->addMonths(4),
            'sort_order' => fake()->numberBetween(1, 4),
            'status' => Term::STATUS_ACTIVE,
        ];
    }
}
