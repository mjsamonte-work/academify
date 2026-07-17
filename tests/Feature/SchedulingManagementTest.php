<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\ClassSchedule;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchedulingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrators_can_access_scheduling_pages(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $this->actingAs($admin)->get(route('scheduling.class-schedules.index'))->assertOk()->assertSee('Class Schedules');
        $this->actingAs($admin)->get(route('scheduling.class-schedules.create'))->assertOk()->assertSee('Create Schedule');
    }

    public function test_teachers_can_access_their_own_schedule_page(): void
    {
        $this->seed();
        $teacherUser = User::where('email', 'teacher@academify.local')->firstOrFail();

        $this->actingAs($teacherUser)
            ->get(route('teacher.schedule'))
            ->assertOk()
            ->assertSee('My Schedule')
            ->assertSee('Mathematics')
            ->assertSee('G1-A');
    }

    public function test_students_and_guardians_can_not_access_scheduling_pages(): void
    {
        $this->seed();
        $student = User::where('email', 'student@academify.local')->firstOrFail();
        $guardian = User::where('email', 'guardian@academify.local')->firstOrFail();

        $this->actingAs($student)->get(route('scheduling.class-schedules.index'))->assertForbidden();
        $this->actingAs($guardian)->get(route('scheduling.class-schedules.index'))->assertForbidden();
        $this->actingAs($student)->get(route('teacher.schedule'))->assertForbidden();
        $this->actingAs($guardian)->get(route('teacher.schedule'))->assertForbidden();
    }

    public function test_scheduling_menus_are_permission_driven(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();
        $student = User::where('email', 'student@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Scheduling')
            ->assertSee('Class Schedules')
            ->assertDontSee('My Schedule');

        $this->actingAs($teacher)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('My Schedule')
            ->assertDontSee('Class Schedules');

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Scheduling')
            ->assertDontSee('My Schedule');
    }

    public function test_administrators_can_create_and_update_class_schedules(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $schoolYear = SchoolYear::where('name', '2026-2027')->firstOrFail();
        $section = Section::where('code', 'G1-A')->firstOrFail();
        $subject = Subject::where('code', 'SCI')->firstOrFail();
        $teacher = Teacher::where('employee_number', 'TCH-0001')->firstOrFail();
        $classroom = Classroom::where('code', 'RM-101')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('scheduling.class-schedules.store'), [
            'school_year_id' => $schoolYear->id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'day_of_week' => ClassSchedule::DAY_TUESDAY,
            'starts_at' => '10:00',
            'ends_at' => '11:00',
            'status' => ClassSchedule::STATUS_ACTIVE,
            'notes' => 'Science block.',
        ]);

        $classSchedule = ClassSchedule::where('subject_id', $subject->id)
            ->where('day_of_week', ClassSchedule::DAY_TUESDAY)
            ->firstOrFail();
        $response->assertRedirect(route('scheduling.class-schedules.show', $classSchedule));

        $this->actingAs($admin)->put(route('scheduling.class-schedules.update', $classSchedule), [
            'school_year_id' => $schoolYear->id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'day_of_week' => ClassSchedule::DAY_TUESDAY,
            'starts_at' => '11:00',
            'ends_at' => '12:00',
            'status' => ClassSchedule::STATUS_INACTIVE,
            'notes' => 'Adjusted block.',
        ])->assertRedirect(route('scheduling.class-schedules.show', $classSchedule));

        $this->assertDatabaseHas('class_schedules', [
            'id' => $classSchedule->id,
            'starts_at' => '11:00',
            'status' => ClassSchedule::STATUS_INACTIVE,
            'notes' => 'Adjusted block.',
        ]);
    }

    public function test_time_and_overlap_validation_is_enforced(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $schoolYear = SchoolYear::where('name', '2026-2027')->firstOrFail();
        $section = Section::where('code', 'G1-A')->firstOrFail();
        $subject = Subject::where('code', 'MATH')->firstOrFail();
        $teacher = Teacher::where('employee_number', 'TCH-0001')->firstOrFail();
        $classroom = Classroom::where('code', 'RM-101')->firstOrFail();

        $payload = [
            'school_year_id' => $schoolYear->id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'day_of_week' => ClassSchedule::DAY_MONDAY,
            'starts_at' => '08:30',
            'ends_at' => '09:30',
            'status' => ClassSchedule::STATUS_ACTIVE,
        ];

        $this->actingAs($admin)
            ->post(route('scheduling.class-schedules.store'), $payload)
            ->assertSessionHasErrors(['teacher_id', 'section_id', 'classroom_id']);

        $this->actingAs($admin)
            ->post(route('scheduling.class-schedules.store'), [
                ...$payload,
                'day_of_week' => ClassSchedule::DAY_WEDNESDAY,
                'starts_at' => '10:00',
                'ends_at' => '09:00',
            ])
            ->assertSessionHasErrors(['ends_at']);
    }

    public function test_teacher_must_be_assigned_to_selected_subject(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $schoolYear = SchoolYear::where('name', '2026-2027')->firstOrFail();
        $section = Section::where('code', 'G1-A')->firstOrFail();
        $subject = Subject::firstOrCreate(
            ['code' => 'ENG'],
            ['name' => 'English', 'status' => Subject::STATUS_ACTIVE],
        );
        $teacher = Teacher::where('employee_number', 'TCH-0001')->firstOrFail();

        $this->actingAs($admin)->post(route('scheduling.class-schedules.store'), [
            'school_year_id' => $schoolYear->id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'day_of_week' => ClassSchedule::DAY_THURSDAY,
            'starts_at' => '13:00',
            'ends_at' => '14:00',
            'status' => ClassSchedule::STATUS_ACTIVE,
        ])->assertSessionHasErrors(['teacher_id']);
    }

    public function test_teacher_schedule_only_shows_that_teacher_schedules(): void
    {
        $this->seed();
        $teacherUser = User::where('email', 'teacher@academify.local')->firstOrFail();

        $this->actingAs($teacherUser)
            ->get(route('teacher.schedule'))
            ->assertOk()
            ->assertSee('Mathematics')
            ->assertDontSee('English');
    }

    public function test_section_schedule_shows_selected_section_schedules(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $section = Section::where('code', 'G1-A')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('scheduling.sections.show', $section))
            ->assertOk()
            ->assertSee('Section Schedule')
            ->assertSee('Mathematics')
            ->assertSee('G1-A');
    }
}
