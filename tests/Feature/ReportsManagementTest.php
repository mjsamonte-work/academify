<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrators_can_view_report_index_and_preview(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Reports')
            ->assertSee('Student Master List');

        $this->actingAs($admin)
            ->get(route('reports.show', 'students'))
            ->assertOk()
            ->assertSee('Student Master List')
            ->assertSee('Alex Santos');
    }

    public function test_reports_menu_is_permission_driven(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Reports');

        $this->actingAs($teacher)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Reports');
    }

    public function test_non_administrators_cannot_access_report_pages(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();
        $student = User::where('email', 'student@academify.local')->firstOrFail();
        $guardian = User::where('email', 'guardian@academify.local')->firstOrFail();

        $this->actingAs($teacher)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($student)->get(route('reports.show', 'students'))->assertForbidden();
        $this->actingAs($guardian)->get(route('reports.export', ['report' => 'students', 'format' => 'pdf']))->assertForbidden();
    }

    public function test_report_filters_are_validated(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->from(route('reports.index'))
            ->get(route('reports.show', ['report' => 'students', 'section_id' => 999999]))
            ->assertRedirect(route('reports.index'))
            ->assertSessionHasErrors(['section_id']);
    }

    public function test_reports_can_export_pdf_and_excel(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('reports.export', ['report' => 'students', 'format' => 'pdf']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($admin)
            ->get(route('reports.export', ['report' => 'students', 'format' => 'excel']))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_report_generation_is_read_only(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $studentCount = Student::count();

        $this->actingAs($admin)->get(route('reports.show', 'students'))->assertOk();
        $this->actingAs($admin)->get(route('reports.export', ['report' => 'students', 'format' => 'excel']))->assertOk();

        $this->assertSame($studentCount, Student::count());
    }
}
