<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_number' => fake()->unique()->numerify('TCH-####'),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->lastName(),
            'last_name' => fake()->lastName(),
            'preferred_name' => fake()->optional()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'address' => fake()->optional()->address(),
            'job_title' => fake()->optional()->jobTitle(),
            'department' => fake()->optional()->randomElement(['Elementary', 'Science', 'Mathematics', 'Language']),
            'employment_type' => fake()->randomElement([
                Teacher::EMPLOYMENT_FULL_TIME,
                Teacher::EMPLOYMENT_PART_TIME,
                Teacher::EMPLOYMENT_CONTRACT,
            ]),
            'hired_at' => fake()->dateTimeBetween('-8 years', 'now'),
            'status' => Teacher::STATUS_ACTIVE,
        ];
    }
}
