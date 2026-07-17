<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrators_can_access_teacher_management_pages(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $this->actingAs($admin)->get(route('records.teachers.index'))->assertOk()->assertSee('Teachers');
        $this->actingAs($admin)->get(route('records.teachers.create'))->assertOk()->assertSee('Create Teacher');
    }

    public function test_teachers_can_access_their_own_teacher_profile(): void
    {
        $this->seed();
        $teacherUser = User::where('email', 'teacher@academify.local')->firstOrFail();

        $this->actingAs($teacherUser)
            ->get(route('teacher.profile'))
            ->assertOk()
            ->assertSee('My Teacher Profile')
            ->assertSee('TCH-0001')
            ->assertSee('Mathematics');
    }

    public function test_students_and_guardians_can_not_access_teacher_management_pages(): void
    {
        $this->seed();
        $student = User::where('email', 'student@academify.local')->firstOrFail();
        $guardian = User::where('email', 'guardian@academify.local')->firstOrFail();

        $this->actingAs($student)->get(route('records.teachers.index'))->assertForbidden();
        $this->actingAs($guardian)->get(route('records.teachers.index'))->assertForbidden();
        $this->actingAs($student)->get(route('teacher.profile'))->assertForbidden();
        $this->actingAs($guardian)->get(route('teacher.profile'))->assertForbidden();
    }

    public function test_teacher_menus_are_permission_driven(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();
        $student = User::where('email', 'student@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Records')
            ->assertSee('Teachers')
            ->assertDontSee('My Teacher Profile');

        $this->actingAs($teacher)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('My Teacher Profile')
            ->assertDontSee('Students')
            ->assertDontSee('Guardians');

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Teachers')
            ->assertDontSee('My Teacher Profile');
    }

    public function test_administrators_can_create_and_update_teachers_with_account_and_subjects(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $linkedUser = User::factory()->create(['email' => 'new.teacher@academify.local']);
        $mathematics = Subject::where('code', 'MATH')->firstOrFail();
        $science = Subject::where('code', 'SCI')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('records.teachers.store'), [
            'employee_number' => 'TCH-9001',
            'user_id' => $linkedUser->id,
            'first_name' => 'Jordan',
            'last_name' => 'Reyes',
            'email' => 'jordan.reyes@academify.local',
            'phone' => '555-9001',
            'job_title' => 'Science Teacher',
            'department' => 'Science',
            'employment_type' => Teacher::EMPLOYMENT_FULL_TIME,
            'hired_at' => '2026-07-01',
            'status' => Teacher::STATUS_ACTIVE,
            'subject_ids' => [$mathematics->id, $science->id],
        ]);

        $teacher = Teacher::where('employee_number', 'TCH-9001')->firstOrFail();
        $response->assertRedirect(route('records.teachers.show', $teacher));

        $this->assertTrue($linkedUser->fresh()->hasRole('Teacher'));
        $this->assertSame(2, $teacher->subjects()->count());

        $this->actingAs($admin)->put(route('records.teachers.update', $teacher), [
            'employee_number' => 'TCH-9001',
            'user_id' => $linkedUser->id,
            'first_name' => 'Jordan',
            'last_name' => 'Reyes-Santos',
            'email' => 'jordan.reyes@academify.local',
            'phone' => '555-9002',
            'job_title' => 'Science Teacher',
            'department' => 'Science',
            'employment_type' => Teacher::EMPLOYMENT_PART_TIME,
            'hired_at' => '2026-07-01',
            'status' => Teacher::STATUS_INACTIVE,
            'subject_ids' => [$science->id],
        ])->assertRedirect(route('records.teachers.show', $teacher));

        $this->assertDatabaseHas('teachers', [
            'id' => $teacher->id,
            'last_name' => 'Reyes-Santos',
            'phone' => '555-9002',
            'status' => Teacher::STATUS_INACTIVE,
        ]);
        $this->assertSame([$science->id], $teacher->fresh()->subjects()->pluck('subjects.id')->all());
        $this->assertDatabaseHas('subjects', ['id' => $mathematics->id]);
    }

    public function test_teacher_unique_fields_and_linked_account_validation(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $existingTeacher = Teacher::where('employee_number', 'TCH-0001')->firstOrFail();

        $this->actingAs($admin)->post(route('records.teachers.store'), [
            'employee_number' => $existingTeacher->employee_number,
            'user_id' => $existingTeacher->user_id,
            'first_name' => 'Duplicate',
            'last_name' => 'Teacher',
            'email' => $existingTeacher->email,
            'employment_type' => 'temporary',
            'status' => Teacher::STATUS_ACTIVE,
        ])->assertSessionHasErrors(['employee_number', 'user_id', 'email', 'employment_type']);
    }
}
