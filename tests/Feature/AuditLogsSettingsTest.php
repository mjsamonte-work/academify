<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\GradeLevel;
use App\Models\GradingPeriod;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogsSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrators_can_access_audit_logs_and_settings(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('administration.audit-logs.index'))
            ->assertOk()
            ->assertSee('Audit Logs');

        $this->actingAs($admin)
            ->get(route('administration.settings.edit'))
            ->assertOk()
            ->assertSee('System Settings')
            ->assertSee('Academify School');
    }

    public function test_non_administrators_are_blocked_from_audit_logs_and_settings(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();
        $student = User::where('email', 'student@academify.local')->firstOrFail();
        $guardian = User::where('email', 'guardian@academify.local')->firstOrFail();

        $this->actingAs($teacher)->get(route('administration.audit-logs.index'))->assertForbidden();
        $this->actingAs($student)->get(route('administration.settings.edit'))->assertForbidden();
        $this->actingAs($guardian)->put(route('administration.settings.update'), [])->assertForbidden();
    }

    public function test_administration_menu_is_permission_driven(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Administration')
            ->assertSee('Audit Logs');

        $this->actingAs($teacher)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Administration')
            ->assertDontSee('Audit Logs');
    }

    public function test_audited_workflows_create_activity_logs(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $gradeLevel = GradeLevel::where('code', 'G1')->firstOrFail();
        $section = Section::where('code', 'G1-A')->firstOrFail();

        $this->actingAs($admin)->post(route('records.students.store'), [
            'student_number' => 'STU-9101',
            'first_name' => 'Morgan',
            'last_name' => 'Lopez',
            'birthdate' => '2017-02-01',
            'gender' => 'Female',
            'email' => 'morgan.lopez@student.academify.local',
            'grade_level_id' => $gradeLevel->id,
            'section_id' => $section->id,
            'status' => Student::STATUS_ACTIVE,
        ]);

        $student = Student::where('student_number', 'STU-9101')->firstOrFail();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => ActivityLog::ACTION_CREATED,
            'module' => ActivityLog::MODULE_STUDENTS,
            'subject_type' => Student::class,
            'subject_id' => $student->id,
            'subject_label' => 'STU-9101',
        ]);

        $activityLog = ActivityLog::where('subject_id', $student->id)->where('subject_type', Student::class)->firstOrFail();

        $this->actingAs($admin)
            ->get(route('administration.audit-logs.show', $activityLog))
            ->assertOk()
            ->assertSee('STU-9101');
    }

    public function test_settings_can_be_updated_and_are_audited(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $schoolYear = SchoolYear::where('name', '2026-2027')->firstOrFail();
        $gradingPeriod = GradingPeriod::where('name', 'First Quarter')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('administration.settings.update'), [
                'school_name' => 'Academify Learning Center',
                'school_code' => 'ALC',
                'school_email' => 'admin@academify.local',
                'school_phone' => '555-0199',
                'school_address' => 'Updated Campus Address',
                'active_school_year_id' => $schoolYear->id,
                'default_grading_period_id' => $gradingPeriod->id,
                'timezone_label' => 'Asia/Manila',
                'report_footer_text' => 'Official Academify report.',
                'report_prepared_by_label' => 'Registrar Office',
            ])
            ->assertRedirect(route('administration.settings.edit'));

        $this->assertSame(
            'Academify Learning Center',
            Setting::where('key', 'school_name')->firstOrFail()->displayValue(),
        );

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => ActivityLog::ACTION_UPDATED,
            'module' => ActivityLog::MODULE_SETTINGS,
            'subject_label' => 'school_name',
        ]);
    }

    public function test_settings_and_audit_filters_validate_inputs(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('administration.settings.update'), [
                'school_name' => '',
                'school_email' => 'not-an-email',
                'active_school_year_id' => 999999,
                'default_grading_period_id' => 999999,
            ])
            ->assertSessionHasErrors(['school_name', 'school_email', 'active_school_year_id', 'default_grading_period_id']);

        $this->actingAs($admin)
            ->from(route('administration.audit-logs.index'))
            ->get(route('administration.audit-logs.index', [
                'module' => 'unknown',
                'date_from' => '2026-07-17',
                'date_to' => '2026-07-16',
            ]))
            ->assertRedirect(route('administration.audit-logs.index'))
            ->assertSessionHasErrors(['module', 'date_to']);
    }
}
