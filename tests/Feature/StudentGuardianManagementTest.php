<?php

namespace Tests\Feature;

use App\Models\GradeLevel;
use App\Models\Guardian;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentGuardianManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrators_can_access_student_and_guardian_pages(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $this->actingAs($admin)->get(route('records.students.index'))->assertOk()->assertSee('Students');
        $this->actingAs($admin)->get(route('records.guardians.index'))->assertOk()->assertSee('Guardians');
    }

    public function test_non_authorized_roles_can_not_access_student_and_guardian_pages(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();

        $this->actingAs($teacher)->get(route('records.students.index'))->assertForbidden();
        $this->actingAs($teacher)->get(route('records.guardians.index'))->assertForbidden();
    }

    public function test_records_menu_is_visible_only_to_authorized_roles(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Records')
            ->assertSee('Students')
            ->assertSee('Guardians');

        $this->actingAs($teacher)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Records')
            ->assertDontSee('Students')
            ->assertDontSee('Guardians');
    }

    public function test_administrators_can_create_and_update_students(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $gradeLevel = GradeLevel::where('code', 'G1')->firstOrFail();
        $section = Section::where('code', 'G1-A')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('records.students.store'), [
            'student_number' => 'STU-9001',
            'first_name' => 'Jamie',
            'last_name' => 'Reyes',
            'birthdate' => '2017-01-01',
            'gender' => 'Female',
            'email' => 'jamie.reyes@student.academify.local',
            'grade_level_id' => $gradeLevel->id,
            'section_id' => $section->id,
            'status' => Student::STATUS_ACTIVE,
        ]);

        $student = Student::where('student_number', 'STU-9001')->firstOrFail();
        $response->assertRedirect(route('records.students.show', $student));

        $this->actingAs($admin)->put(route('records.students.update', $student), [
            'student_number' => 'STU-9001',
            'first_name' => 'Jamie',
            'last_name' => 'Reyes-Santos',
            'birthdate' => '2017-01-01',
            'gender' => 'Female',
            'email' => 'jamie.reyes@student.academify.local',
            'grade_level_id' => $gradeLevel->id,
            'section_id' => $section->id,
            'status' => Student::STATUS_INACTIVE,
        ])->assertRedirect(route('records.students.show', $student));

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'last_name' => 'Reyes-Santos',
            'status' => Student::STATUS_INACTIVE,
        ]);
    }

    public function test_administrators_can_create_and_update_guardians(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('records.guardians.store'), [
            'first_name' => 'Rosa',
            'last_name' => 'Dela Cruz',
            'email' => 'rosa.delacruz@example.test',
            'phone' => '555-0200',
            'status' => Guardian::STATUS_ACTIVE,
        ]);

        $guardian = Guardian::where('email', 'rosa.delacruz@example.test')->firstOrFail();
        $response->assertRedirect(route('records.guardians.show', $guardian));

        $this->actingAs($admin)->put(route('records.guardians.update', $guardian), [
            'first_name' => 'Rosa',
            'last_name' => 'Dela Cruz',
            'email' => 'rosa.delacruz@example.test',
            'phone' => '555-0201',
            'status' => Guardian::STATUS_INACTIVE,
        ])->assertRedirect(route('records.guardians.show', $guardian));

        $this->assertDatabaseHas('guardians', [
            'id' => $guardian->id,
            'phone' => '555-0201',
            'status' => Guardian::STATUS_INACTIVE,
        ]);
    }

    public function test_student_number_and_grade_section_validation(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $student = Student::where('student_number', 'STU-0001')->firstOrFail();
        $gradeTwo = GradeLevel::where('code', 'G2')->firstOrFail();
        $section = Section::where('code', 'G1-A')->firstOrFail();

        $this->actingAs($admin)->post(route('records.students.store'), [
            'student_number' => $student->student_number,
            'first_name' => 'Duplicate',
            'last_name' => 'Student',
            'grade_level_id' => $gradeTwo->id,
            'section_id' => $section->id,
            'status' => Student::STATUS_ACTIVE,
        ])->assertSessionHasErrors(['student_number', 'section_id']);
    }

    public function test_students_and_guardians_can_be_linked_and_detached_without_deleting_records(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $student = Student::where('student_number', 'STU-0001')->firstOrFail();
        $guardian = Guardian::factory()->create(['email' => 'second.guardian@example.test']);

        $this->actingAs($admin)->post(route('records.students.guardians.store', $student), [
            'guardian_id' => $guardian->id,
            'relationship' => 'Father',
            'is_primary_contact' => '1',
            'can_pick_up' => '1',
            'receives_notifications' => '1',
        ])->assertRedirect(route('records.students.show', $student));

        $this->assertDatabaseHas('guardian_student', [
            'student_id' => $student->id,
            'guardian_id' => $guardian->id,
            'relationship' => 'Father',
            'is_primary_contact' => true,
        ]);

        $this->assertSame(1, $student->fresh()->guardians()->wherePivot('is_primary_contact', true)->count());

        $this->actingAs($admin)
            ->delete(route('records.students.guardians.destroy', [$student, $guardian]))
            ->assertRedirect(route('records.students.show', $student));

        $this->assertDatabaseMissing('guardian_student', [
            'student_id' => $student->id,
            'guardian_id' => $guardian->id,
        ]);
        $this->assertDatabaseHas('students', ['id' => $student->id]);
        $this->assertDatabaseHas('guardians', ['id' => $guardian->id]);
    }
}
