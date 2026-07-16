<?php

namespace Database\Factories;

use App\Models\GradeLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradeLevel>
 */
class GradeLevelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $level = fake()->unique()->numberBetween(1, 12);

        return [
            'name' => "Grade {$level}",
            'code' => "G{$level}",
            'sort_order' => $level,
            'status' => GradeLevel::STATUS_ACTIVE,
        ];
    }
}
