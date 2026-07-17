<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\GradeLevel;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrators_can_access_enrollment_pages(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $this->actingAs($admin)->get(route('enrollment.enrollments.index'))->assertOk()->assertSee('Enrollments');
        $this->actingAs($admin)->get(route('enrollment.enrollments.create'))->assertOk()->assertSee('Create Enrollment');
    }

    public function test_non_authorized_roles_can_not_access_enrollment_pages(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();
        $student = User::where('email', 'student@academify.local')->firstOrFail();
        $guardian = User::where('email', 'guardian@academify.local')->firstOrFail();

        $this->actingAs($teacher)->get(route('enrollment.enrollments.index'))->assertForbidden();
        $this->actingAs($student)->get(route('enrollment.enrollments.index'))->assertForbidden();
        $this->actingAs($guardian)->get(route('enrollment.enrollments.index'))->assertForbidden();
    }

    public function test_enrollment_menu_is_visible_only_to_authorized_roles(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Enrollment')
            ->assertSee('Enrollments');

        $this->actingAs($teacher)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Enrollment')
            ->assertDontSee('Enrollments');
    }

    public function test_administrators_can_create_and_update_enrollments(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $student = Student::create([
            'student_number' => 'STU-9002',
            'first_name' => 'Nico',
            'last_name' => 'Reyes',
            'status' => Student::STATUS_ACTIVE,
        ]);
        $schoolYear = SchoolYear::where('name', '2026-2027')->firstOrFail();
        $gradeLevel = GradeLevel::where('code', 'G1')->firstOrFail();
        $section = Section::where('code', 'G1-A')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('enrollment.enrollments.store'), [
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => $gradeLevel->id,
            'section_id' => $section->id,
            'status' => Enrollment::STATUS_ENROLLED,
            'enrolled_at' => '2026-06-15',
            'notes' => 'Initial enrollment.',
        ]);

        $enrollment = Enrollment::where('student_id', $student->id)->firstOrFail();
        $response->assertRedirect(route('enrollment.enrollments.show', $enrollment));

        $this->actingAs($admin)->put(route('enrollment.enrollments.update', $enrollment), [
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => $gradeLevel->id,
            'section_id' => $section->id,
            'status' => Enrollment::STATUS_WITHDRAWN,
            'enrolled_at' => '2026-06-15',
            'withdrawn_at' => '2026-08-01',
            'notes' => 'Transferred.',
        ])->assertRedirect(route('enrollment.enrollments.show', $enrollment));

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => Enrollment::STATUS_WITHDRAWN,
            'notes' => 'Transferred.',
        ]);
    }

    public function test_duplicate_enrolled_record_and_invalid_section_are_rejected(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $student = Student::where('student_number', 'STU-0001')->firstOrFail();
        $schoolYear = SchoolYear::where('name', '2026-2027')->firstOrFail();
        $gradeTwo = GradeLevel::where('code', 'G2')->firstOrFail();
        $section = Section::where('code', 'G1-A')->firstOrFail();

        $this->actingAs($admin)->post(route('enrollment.enrollments.store'), [
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => $gradeTwo->id,
            'section_id' => $section->id,
            'status' => Enrollment::STATUS_ENROLLED,
            'enrolled_at' => '2026-06-15',
        ])->assertSessionHasErrors(['student_id', 'section_id']);
    }

    public function test_status_date_rules_are_enforced(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $student = Student::where('student_number', 'STU-0001')->firstOrFail();
        $schoolYear = SchoolYear::where('name', '2026-2027')->firstOrFail();
        $gradeLevel = GradeLevel::where('code', 'G1')->firstOrFail();
        $section = Section::where('code', 'G1-A')->firstOrFail();

        $this->actingAs($admin)->post(route('enrollment.enrollments.store'), [
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => $gradeLevel->id,
            'section_id' => $section->id,
            'status' => Enrollment::STATUS_WITHDRAWN,
        ])->assertSessionHasErrors(['withdrawn_at']);

        $this->actingAs($admin)->post(route('enrollment.enrollments.store'), [
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'grade_level_id' => $gradeLevel->id,
            'section_id' => $section->id,
            'status' => Enrollment::STATUS_COMPLETED,
        ])->assertSessionHasErrors(['completed_at']);
    }

    public function test_enrollment_history_is_visible_on_detail_page(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $enrollment = Enrollment::with('student')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('enrollment.enrollments.show', $enrollment))
            ->assertOk()
            ->assertSee('Student Enrollment History')
            ->assertSee($enrollment->student->student_number)
            ->assertSee('2026-2027');
    }
}
