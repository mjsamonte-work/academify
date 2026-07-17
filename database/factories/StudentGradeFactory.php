<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Student;
use App\Models\StudentGrade;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentGrade>
 */
class StudentGradeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assessment_id' => Assessment::factory(),
            'student_id' => Student::factory(),
            'score' => fake()->numberBetween(75, 100),
            'remarks' => fake()->optional()->sentence(),
            'status' => StudentGrade::STATUS_DRAFT,
            'submitted_by' => null,
            'submitted_at' => null,
        ];
    }
}
