<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\SaveAttendanceRequest;
use App\Models\AttendanceSession;
use App\Models\ClassSchedule;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(): View
    {
        $teacher = request()->user()?->teacher()->first();

        return view('teacher.attendance.index', [
            'teacher' => $teacher,
            'classSchedules' => $teacher
                ? ClassSchedule::query()
                    ->with(['schoolYear', 'section.gradeLevel', 'subject', 'classroom'])
                    ->where('teacher_id', $teacher->id)
                    ->where('status', ClassSchedule::STATUS_ACTIVE)
                    ->orderBy('day_of_week')
                    ->orderBy('starts_at')
                    ->get()
                : collect(),
        ]);
    }

    public function show(Request $request, ClassSchedule $classSchedule): View
    {
        $this->authorizeSchedule($classSchedule);

        $date = $request->date('attendance_date')?->toDateString() ?? now()->toDateString();
        $classSchedule->load(['schoolYear', 'section.gradeLevel', 'subject', 'classroom', 'teacher']);
        $session = AttendanceSession::query()
            ->with('records')
            ->where('class_schedule_id', $classSchedule->id)
            ->whereDate('attendance_date', $date)
            ->first();

        $students = $this->studentsForSchedule($classSchedule);

        return view('teacher.attendance.show', [
            'classSchedule' => $classSchedule,
            'session' => $session,
            'students' => $students,
            'attendanceDate' => $date,
            'recordsByStudent' => $session?->records->keyBy('student_id') ?? collect(),
        ]);
    }

    public function save(SaveAttendanceRequest $request, ClassSchedule $classSchedule): RedirectResponse
    {
        $validated = $request->validated();
        $status = $validated['session_status'];

        $session = AttendanceSession::query()
            ->where('class_schedule_id', $classSchedule->id)
            ->whereDate('attendance_date', $validated['attendance_date'])
            ->first() ?? new AttendanceSession([
                'class_schedule_id' => $classSchedule->id,
                'attendance_date' => $validated['attendance_date'],
            ]);

        $session->fill([
            'status' => $status,
            'notes' => $validated['notes'] ?? null,
            'submitted_by' => $status === AttendanceSession::STATUS_SUBMITTED ? $request->user()?->id : null,
            'submitted_at' => $status === AttendanceSession::STATUS_SUBMITTED ? now() : null,
        ])->save();

        foreach ($validated['records'] as $record) {
            $session->records()->updateOrCreate(
                ['student_id' => $record['student_id']],
                [
                    'status' => $record['status'],
                    'notes' => $record['notes'] ?? null,
                ],
            );
        }

        return redirect()->route('teacher.attendance.show', [
            'classSchedule' => $classSchedule,
            'attendance_date' => $validated['attendance_date'],
        ])->with('status', $status === AttendanceSession::STATUS_SUBMITTED
            ? 'Attendance session submitted.'
            : 'Attendance draft saved.');
    }

    private function authorizeSchedule(ClassSchedule $classSchedule): void
    {
        abort_unless(
            request()->user()?->teacher?->id === $classSchedule->teacher_id,
            403,
        );
    }

    /**
     * @return Collection<int, Student>
     */
    private function studentsForSchedule(ClassSchedule $classSchedule): Collection
    {
        return Enrollment::query()
            ->with('student')
            ->where('school_year_id', $classSchedule->school_year_id)
            ->where('section_id', $classSchedule->section_id)
            ->where('status', Enrollment::STATUS_ENROLLED)
            ->orderBy('student_id')
            ->get()
            ->pluck('student');
    }
}
