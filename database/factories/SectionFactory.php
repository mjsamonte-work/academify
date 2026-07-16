<?php

namespace Database\Factories;

use App\Models\GradeLevel;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Section>
 */
class SectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'grade_level_id' => GradeLevel::factory(),
            'name' => fake()->randomElement(['A', 'B', 'C']),
            'code' => fake()->unique()->bothify('SEC-##??'),
            'capacity' => fake()->numberBetween(25, 45),
            'status' => Section::STATUS_ACTIVE,
        ];
    }
}
