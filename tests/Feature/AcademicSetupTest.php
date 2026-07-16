<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrators_can_access_academic_setup_pages(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('academic.school-years.index'))
            ->assertOk()
            ->assertSee('School Years');
    }

    public function test_non_administrators_can_not_access_academic_setup_pages(): void
    {
        $this->seed();

        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();

        $this->actingAs($teacher)
            ->get(route('academic.school-years.index'))
            ->assertForbidden();
    }

    public function test_academic_setup_menu_is_visible_only_to_authorized_roles(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Academic Setup')
            ->assertSee('School Years')
            ->assertSee('Classrooms');

        $this->actingAs($teacher)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Academic Setup')
            ->assertDontSee('School Years')
            ->assertDontSee('Classrooms');
    }

    public function test_administrators_can_create_academic_setup_records(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $schoolYear = SchoolYear::firstOrCreate([
            'name' => '2027-2028',
        ], [
            'starts_at' => '2027-06-01',
            'ends_at' => '2028-03-31',
            'is_active' => false,
            'status' => SchoolYear::STATUS_ACTIVE,
        ]);
        $gradeLevel = GradeLevel::firstOrCreate([
            'code' => 'G3',
        ], [
            'name' => 'Grade 3',
            'sort_order' => 3,
            'status' => GradeLevel::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin);

        $this->post(route('academic.school-years.store'), [
            'name' => '2028-2029',
            'starts_at' => '2028-06-01',
            'ends_at' => '2029-03-31',
            'is_active' => '1',
            'status' => SchoolYear::STATUS_ACTIVE,
        ])->assertRedirect();

        $this->post(route('academic.terms.store'), [
            'school_year_id' => $schoolYear->id,
            'name' => 'Summer Term',
            'starts_at' => '2027-06-01',
            'ends_at' => '2027-07-31',
            'sort_order' => 3,
            'status' => Term::STATUS_ACTIVE,
        ])->assertRedirect();

        $this->post(route('academic.grade-levels.store'), [
            'name' => 'Grade 4',
            'code' => 'G4',
            'sort_order' => 4,
            'status' => GradeLevel::STATUS_ACTIVE,
        ])->assertRedirect();

        $this->post(route('academic.sections.store'), [
            'grade_level_id' => $gradeLevel->id,
            'name' => 'Section B',
            'code' => 'G3-B',
            'capacity' => 40,
            'status' => Section::STATUS_ACTIVE,
        ])->assertRedirect();

        $this->post(route('academic.subjects.store'), [
            'name' => 'English',
            'code' => 'ENG',
            'description' => 'Language subject',
            'status' => Subject::STATUS_ACTIVE,
        ])->assertRedirect();

        $this->post(route('academic.classrooms.store'), [
            'name' => 'Room 202',
            'code' => 'RM-202',
            'capacity' => 40,
            'location' => 'Annex',
            'status' => Classroom::STATUS_ACTIVE,
        ])->assertRedirect();

        $this->assertDatabaseHas('school_years', ['name' => '2028-2029', 'is_active' => true]);
        $this->assertDatabaseHas('terms', ['name' => 'Summer Term']);
        $this->assertDatabaseHas('grade_levels', ['code' => 'G4']);
        $this->assertDatabaseHas('sections', ['code' => 'G3-B']);
        $this->assertDatabaseHas('subjects', ['code' => 'ENG']);
        $this->assertDatabaseHas('classrooms', ['code' => 'RM-202']);
    }

    public function test_administrators_can_update_academic_setup_records(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $subject = Subject::where('code', 'MATH')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('academic.subjects.update', $subject), [
                'name' => 'Advanced Mathematics',
                'code' => 'MATH-ADV',
                'description' => 'Updated subject description',
                'status' => Subject::STATUS_INACTIVE,
            ])
            ->assertRedirect(route('academic.subjects.edit', $subject));

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'Advanced Mathematics',
            'code' => 'MATH-ADV',
            'status' => Subject::STATUS_INACTIVE,
        ]);
    }

    public function test_only_one_school_year_can_be_active(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $existing = SchoolYear::where('is_active', true)->firstOrFail();
        $next = SchoolYear::factory()->create([
            'name' => '2030-2031',
            'starts_at' => '2030-06-01',
            'ends_at' => '2031-03-31',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->put(route('academic.school-years.update', $next), [
                'name' => $next->name,
                'starts_at' => '2030-06-01',
                'ends_at' => '2031-03-31',
                'is_active' => '1',
                'status' => SchoolYear::STATUS_ACTIVE,
            ])
            ->assertRedirect();

        $this->assertFalse($existing->fresh()->is_active);
        $this->assertTrue($next->fresh()->is_active);
    }

    public function test_term_dates_must_stay_inside_selected_school_year(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $schoolYear = SchoolYear::where('name', '2026-2027')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('academic.terms.store'), [
                'school_year_id' => $schoolYear->id,
                'name' => 'Invalid Term',
                'starts_at' => '2026-05-01',
                'ends_at' => '2026-07-31',
                'sort_order' => 9,
                'status' => Term::STATUS_ACTIVE,
            ])
            ->assertSessionHasErrors('starts_at');
    }

    public function test_duplicate_codes_are_rejected(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('academic.subjects.store'), [
                'name' => 'Duplicate Mathematics',
                'code' => 'MATH',
                'description' => 'Duplicate code',
                'status' => Subject::STATUS_ACTIVE,
            ])
            ->assertSessionHasErrors('code');
    }
}
