<?php

namespace Database\Factories;

use App\Models\GradeLevel;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_number' => fake()->unique()->numerify('STU-####'),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->lastName(),
            'last_name' => fake()->lastName(),
            'preferred_name' => fake()->optional()->firstName(),
            'birthdate' => fake()->dateTimeBetween('-18 years', '-5 years'),
            'gender' => fake()->randomElement(['Female', 'Male']),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'address' => fake()->optional()->address(),
            'grade_level_id' => GradeLevel::factory(),
            'section_id' => Section::factory(),
            'status' => Student::STATUS_ACTIVE,
        ];
    }
}
