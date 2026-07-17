<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\GradeLevel;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'school_year_id' => SchoolYear::factory(),
            'grade_level_id' => GradeLevel::factory(),
            'section_id' => Section::factory(),
            'status' => Enrollment::STATUS_PENDING,
            'enrolled_at' => null,
            'withdrawn_at' => null,
            'completed_at' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
