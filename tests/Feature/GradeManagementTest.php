<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\ClassSchedule;
use App\Models\GradingPeriod;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrators_can_access_grade_management_pages(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $this->actingAs($admin)->get(route('grades.grading-periods.index'))->assertOk()->assertSee('Grading Periods');
        $this->actingAs($admin)->get(route('grades.assessments.index'))->assertOk()->assertSee('Assessments');
    }

    public function test_teachers_can_access_grade_entry_for_their_own_schedules(): void
    {
        $this->seed();
        $teacherUser = User::where('email', 'teacher@academify.local')->firstOrFail();
        $classSchedule = ClassSchedule::firstOrFail();
        $assessment = Assessment::firstOrFail();

        $this->actingAs($teacherUser)->get(route('teacher.grades.index'))->assertOk()->assertSee('Grade Entry');
        $this->actingAs($teacherUser)->get(route('teacher.grades.show', $classSchedule))->assertOk()->assertSee('Class Grades');
        $this->actingAs($teacherUser)->get(route('teacher.grades.assessments.edit', $assessment))->assertOk()->assertSee('Alex Santos');
    }

    public function test_teachers_can_not_access_another_teacher_grade_entry(): void
    {
        $this->seed();
        $teacherUser = User::where('email', 'teacher@academify.local')->firstOrFail();
        $otherTeacher = Teacher::factory()->create();
        $subject = Subject::where('code', 'MATH')->firstOrFail();
        $otherTeacher->subjects()->attach($subject);
        $classSchedule = ClassSchedule::factory()->create([
            'school_year_id' => SchoolYear::firstOrFail()->id,
            'section_id' => Section::where('code', 'G1-A')->firstOrFail()->id,
            'subject_id' => $subject->id,
            'teacher_id' => $otherTeacher->id,
            'classroom_id' => null,
            'day_of_week' => ClassSchedule::DAY_FRIDAY,
            'starts_at' => '14:00',
            'ends_at' => '15:00',
        ]);

        $this->actingAs($teacherUser)->get(route('teacher.grades.show', $classSchedule))->assertForbidden();
    }

    public function test_students_and_guardians_can_not_access_grade_pages(): void
    {
        $this->seed();
        $student = User::where('email', 'student@academify.local')->firstOrFail();
        $guardian = User::where('email', 'guardian@academify.local')->firstOrFail();

        $this->actingAs($student)->get(route('grades.assessments.index'))->assertForbidden();
        $this->actingAs($guardian)->get(route('grades.assessments.index'))->assertForbidden();
        $this->actingAs($student)->get(route('teacher.grades.index'))->assertForbidden();
        $this->actingAs($guardian)->get(route('teacher.grades.index'))->assertForbidden();
    }

    public function test_grade_menus_are_permission_driven(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();
        $student = User::where('email', 'student@academify.local')->firstOrFail();

        $this->actingAs($admin)->get(route('dashboard'))->assertOk()->assertSee('Grades')->assertSee('Assessments')->assertDontSee('Grade Entry');
        $this->actingAs($teacher)->get(route('dashboard'))->assertOk()->assertSee('Grade Entry')->assertDontSee('Grading Periods');
        $this->actingAs($student)->get(route('dashboard'))->assertOk()->assertDontSee('Grade Entry')->assertDontSee('Assessments');
    }

    public function test_administrators_can_create_and_update_grading_periods(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $schoolYear = SchoolYear::where('name', '2026-2027')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('grades.grading-periods.store'), [
            'school_year_id' => $schoolYear->id,
            'name' => 'Second Quarter',
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-10-31',
            'sort_order' => 2,
            'status' => GradingPeriod::STATUS_ACTIVE,
        ]);

        $period = GradingPeriod::where('name', 'Second Quarter')->firstOrFail();
        $response->assertRedirect(route('grades.grading-periods.show', $period));

        $this->actingAs($admin)->put(route('grades.grading-periods.update', $period), [
            'school_year_id' => $schoolYear->id,
            'name' => 'Second Quarter',
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-10-30',
            'sort_order' => 2,
            'status' => GradingPeriod::STATUS_INACTIVE,
        ])->assertRedirect(route('grades.grading-periods.show', $period));

        $this->assertDatabaseHas('grading_periods', ['id' => $period->id, 'status' => GradingPeriod::STATUS_INACTIVE]);
    }

    public function test_invalid_grading_period_dates_are_rejected(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $schoolYear = SchoolYear::where('name', '2026-2027')->firstOrFail();

        $this->actingAs($admin)->post(route('grades.grading-periods.store'), [
            'school_year_id' => $schoolYear->id,
            'name' => 'Outside Period',
            'starts_at' => '2025-01-01',
            'ends_at' => '2025-02-01',
            'sort_order' => 3,
            'status' => GradingPeriod::STATUS_ACTIVE,
        ])->assertSessionHasErrors(['starts_at']);
    }

    public function test_administrators_can_create_and_update_assessments(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $classSchedule = ClassSchedule::firstOrFail();
        $period = GradingPeriod::firstOrFail();

        $response = $this->actingAs($admin)->post(route('grades.assessments.store'), [
            'class_schedule_id' => $classSchedule->id,
            'grading_period_id' => $period->id,
            'title' => 'Activity 1',
            'assessment_type' => 'activity',
            'max_score' => 50,
            'weight' => 20,
            'due_date' => '2026-06-20',
            'status' => Assessment::STATUS_DRAFT,
        ]);

        $assessment = Assessment::where('title', 'Activity 1')->firstOrFail();
        $response->assertRedirect(route('grades.assessments.show', $assessment));

        $this->actingAs($admin)->put(route('grades.assessments.update', $assessment), [
            'class_schedule_id' => $classSchedule->id,
            'grading_period_id' => $period->id,
            'title' => 'Activity 1 Updated',
            'assessment_type' => 'activity',
            'max_score' => 60,
            'weight' => 20,
            'due_date' => '2026-06-21',
            'status' => Assessment::STATUS_SUBMITTED,
        ])->assertRedirect(route('grades.assessments.show', $assessment));

        $this->assertDatabaseHas('assessments', ['id' => $assessment->id, 'title' => 'Activity 1 Updated', 'max_score' => 60]);
    }

    public function test_assessment_school_year_must_match_grading_period(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $classSchedule = ClassSchedule::firstOrFail();
        $otherYear = SchoolYear::create(['name' => '2027-2028', 'starts_at' => '2027-06-01', 'ends_at' => '2028-03-31', 'is_active' => false, 'status' => SchoolYear::STATUS_ACTIVE]);
        $period = GradingPeriod::create(['school_year_id' => $otherYear->id, 'name' => 'Other Quarter', 'sort_order' => 1, 'status' => GradingPeriod::STATUS_ACTIVE]);

        $this->actingAs($admin)->post(route('grades.assessments.store'), [
            'class_schedule_id' => $classSchedule->id,
            'grading_period_id' => $period->id,
            'title' => 'Mismatch',
            'max_score' => 100,
            'weight' => 10,
            'status' => Assessment::STATUS_DRAFT,
        ])->assertSessionHasErrors(['grading_period_id']);
    }

    public function test_teachers_can_save_and_submit_student_grades(): void
    {
        $this->seed();
        $teacherUser = User::where('email', 'teacher@academify.local')->firstOrFail();
        $assessment = Assessment::firstOrFail();
        $student = Student::where('student_number', 'STU-0001')->firstOrFail();

        $this->actingAs($teacherUser)->post(route('teacher.grades.assessments.save', $assessment), [
            'grade_status' => StudentGrade::STATUS_DRAFT,
            'grades' => [
                ['student_id' => $student->id, 'score' => 88, 'remarks' => 'Draft score.'],
            ],
        ])->assertRedirect(route('teacher.grades.assessments.edit', $assessment));

        $this->assertDatabaseHas('student_grades', ['assessment_id' => $assessment->id, 'student_id' => $student->id, 'score' => 88, 'status' => StudentGrade::STATUS_DRAFT]);

        $this->actingAs($teacherUser)->post(route('teacher.grades.assessments.save', $assessment), [
            'grade_status' => StudentGrade::STATUS_SUBMITTED,
            'grades' => [
                ['student_id' => $student->id, 'score' => 90, 'remarks' => 'Submitted score.'],
            ],
        ])->assertRedirect(route('teacher.grades.assessments.edit', $assessment));

        $this->assertDatabaseHas('student_grades', ['assessment_id' => $assessment->id, 'student_id' => $student->id, 'score' => 90, 'status' => StudentGrade::STATUS_SUBMITTED, 'submitted_by' => $teacherUser->id]);
    }

    public function test_grade_score_and_enrolled_student_validation(): void
    {
        $this->seed();
        $teacherUser = User::where('email', 'teacher@academify.local')->firstOrFail();
        $assessment = Assessment::firstOrFail();
        $student = Student::where('student_number', 'STU-0001')->firstOrFail();
        $outsideStudent = Student::create(['student_number' => 'STU-9922', 'first_name' => 'Outside', 'last_name' => 'Student', 'status' => Student::STATUS_ACTIVE]);

        $this->actingAs($teacherUser)->post(route('teacher.grades.assessments.save', $assessment), [
            'grade_status' => StudentGrade::STATUS_DRAFT,
            'grades' => [
                ['student_id' => $student->id, 'score' => 101, 'remarks' => null],
            ],
        ])->assertSessionHasErrors(['grades.0.score']);

        $this->actingAs($teacherUser)->post(route('teacher.grades.assessments.save', $assessment), [
            'grade_status' => StudentGrade::STATUS_DRAFT,
            'grades' => [
                ['student_id' => $outsideStudent->id, 'score' => 80, 'remarks' => null],
            ],
        ])->assertSessionHasErrors(['grades']);
    }

    public function test_administrators_can_review_assessment_grade_records(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $assessment = Assessment::firstOrFail();

        $this->actingAs($admin)
            ->get(route('grades.assessments.records', $assessment))
            ->assertOk()
            ->assertSee('Grade Records')
            ->assertSee('Alex Santos')
            ->assertSee('95');
    }
}
