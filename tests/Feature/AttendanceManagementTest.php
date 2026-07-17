<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ClassSchedule;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrators_can_access_attendance_pages(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $session = AttendanceSession::firstOrFail();

        $this->actingAs($admin)->get(route('attendance.sessions.index'))->assertOk()->assertSee('Attendance Sessions');
        $this->actingAs($admin)->get(route('attendance.sessions.show', $session))->assertOk()->assertSee('Attendance Records');
    }

    public function test_teachers_can_access_attendance_for_their_own_schedules(): void
    {
        $this->seed();
        $teacherUser = User::where('email', 'teacher@academify.local')->firstOrFail();
        $classSchedule = ClassSchedule::firstOrFail();

        $this->actingAs($teacherUser)
            ->get(route('teacher.attendance.index'))
            ->assertOk()
            ->assertSee('Attendance Entry')
            ->assertSee('Mathematics');

        $this->actingAs($teacherUser)
            ->get(route('teacher.attendance.show', ['classSchedule' => $classSchedule, 'attendance_date' => '2026-06-15']))
            ->assertOk()
            ->assertSee('Alex Santos');
    }

    public function test_teachers_can_not_access_another_teacher_schedule_attendance(): void
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

        $this->actingAs($teacherUser)
            ->get(route('teacher.attendance.show', $classSchedule))
            ->assertForbidden();
    }

    public function test_students_and_guardians_can_not_access_attendance_pages(): void
    {
        $this->seed();
        $student = User::where('email', 'student@academify.local')->firstOrFail();
        $guardian = User::where('email', 'guardian@academify.local')->firstOrFail();

        $this->actingAs($student)->get(route('attendance.sessions.index'))->assertForbidden();
        $this->actingAs($guardian)->get(route('attendance.sessions.index'))->assertForbidden();
        $this->actingAs($student)->get(route('teacher.attendance.index'))->assertForbidden();
        $this->actingAs($guardian)->get(route('teacher.attendance.index'))->assertForbidden();
    }

    public function test_attendance_menus_are_permission_driven(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();
        $student = User::where('email', 'student@academify.local')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Attendance')
            ->assertSee('Attendance Sessions')
            ->assertDontSee('Attendance Entry');

        $this->actingAs($teacher)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Attendance Entry')
            ->assertDontSee('Attendance Sessions');

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Attendance Sessions')
            ->assertDontSee('Attendance Entry');
    }

    public function test_teacher_can_create_draft_and_submit_attendance(): void
    {
        $this->seed();
        $teacherUser = User::where('email', 'teacher@academify.local')->firstOrFail();
        $classSchedule = ClassSchedule::firstOrFail();
        $student = Student::where('student_number', 'STU-0001')->firstOrFail();

        $this->actingAs($teacherUser)->post(route('teacher.attendance.save', $classSchedule), [
            'attendance_date' => '2026-06-15',
            'session_status' => AttendanceSession::STATUS_DRAFT,
            'notes' => 'Initial draft.',
            'records' => [
                ['student_id' => $student->id, 'status' => AttendanceRecord::STATUS_LATE, 'notes' => 'Arrived after bell.'],
            ],
        ])->assertSessionHasNoErrors()->assertRedirect(route('teacher.attendance.show', [
            'classSchedule' => $classSchedule,
            'attendance_date' => '2026-06-15',
        ]));

        $session = AttendanceSession::whereDate('attendance_date', '2026-06-15')->firstOrFail();
        $this->assertSame(AttendanceSession::STATUS_DRAFT, $session->status);
        $this->assertDatabaseHas('attendance_records', [
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => AttendanceRecord::STATUS_LATE,
        ]);

        $this->actingAs($teacherUser)->post(route('teacher.attendance.save', $classSchedule), [
            'attendance_date' => '2026-06-15',
            'session_status' => AttendanceSession::STATUS_SUBMITTED,
            'notes' => 'Submitted.',
            'records' => [
                ['student_id' => $student->id, 'status' => AttendanceRecord::STATUS_PRESENT, 'notes' => null],
            ],
        ])->assertRedirect(route('teacher.attendance.show', [
            'classSchedule' => $classSchedule,
            'attendance_date' => '2026-06-15',
        ]));

        $this->assertDatabaseHas('attendance_sessions', [
            'id' => $session->id,
            'status' => AttendanceSession::STATUS_SUBMITTED,
            'submitted_by' => $teacherUser->id,
        ]);
        $this->assertSame(1, AttendanceSession::where('class_schedule_id', $classSchedule->id)->whereDate('attendance_date', '2026-06-15')->count());
    }

    public function test_attendance_records_reject_students_not_enrolled_in_scheduled_section(): void
    {
        $this->seed();
        $teacherUser = User::where('email', 'teacher@academify.local')->firstOrFail();
        $classSchedule = ClassSchedule::firstOrFail();
        $otherStudent = Student::create([
            'student_number' => 'STU-9911',
            'first_name' => 'Outside',
            'last_name' => 'Student',
            'status' => Student::STATUS_ACTIVE,
        ]);

        $this->actingAs($teacherUser)->post(route('teacher.attendance.save', $classSchedule), [
            'attendance_date' => '2026-06-22',
            'session_status' => AttendanceSession::STATUS_DRAFT,
            'records' => [
                ['student_id' => $otherStudent->id, 'status' => AttendanceRecord::STATUS_PRESENT, 'notes' => null],
            ],
        ])->assertSessionHasErrors(['records']);
    }

    public function test_student_attendance_history_displays_recorded_attendance(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $student = Student::where('student_number', 'STU-0001')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('attendance.students.show', $student))
            ->assertOk()
            ->assertSee('Student Attendance')
            ->assertSee('Mathematics')
            ->assertSee('Present');
    }
}
