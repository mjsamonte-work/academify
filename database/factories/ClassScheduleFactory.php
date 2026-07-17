<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\ClassSchedule;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassSchedule>
 */
class ClassScheduleFactory extends Factory
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
            'section_id' => Section::factory(),
            'subject_id' => Subject::factory(),
            'teacher_id' => Teacher::factory(),
            'classroom_id' => Classroom::factory(),
            'day_of_week' => fake()->randomElement(ClassSchedule::days()),
            'starts_at' => '08:00',
            'ends_at' => '09:00',
            'status' => ClassSchedule::STATUS_ACTIVE,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
