<?php

namespace Database\Factories;

use App\Models\AttendanceSession;
use App\Models\ClassSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceSession>
 */
class AttendanceSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_schedule_id' => ClassSchedule::factory(),
            'attendance_date' => now()->toDateString(),
            'status' => AttendanceSession::STATUS_DRAFT,
            'submitted_by' => null,
            'submitted_at' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
