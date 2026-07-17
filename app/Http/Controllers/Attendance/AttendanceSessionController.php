<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ClassSchedule;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceSessionController extends Controller
{
    public function index(Request $request): View
    {
        $sessions = AttendanceSession::query()
            ->with(['classSchedule.schoolYear', 'classSchedule.section', 'classSchedule.subject', 'classSchedule.teacher', 'submittedBy'])
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('attendance_date', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('attendance_date', '<=', $request->date('date_to')))
            ->when($request->filled('school_year_id'), fn ($query) => $query->whereHas('classSchedule', fn ($query) => $query->where('school_year_id', $request->integer('school_year_id'))))
            ->when($request->filled('section_id'), fn ($query) => $query->whereHas('classSchedule', fn ($query) => $query->where('section_id', $request->integer('section_id'))))
            ->when($request->filled('teacher_id'), fn ($query) => $query->whereHas('classSchedule', fn ($query) => $query->where('teacher_id', $request->integer('teacher_id'))))
            ->when($request->filled('class_schedule_id'), fn ($query) => $query->where('class_schedule_id', $request->integer('class_schedule_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest('attendance_date')
            ->paginate(10)
            ->withQueryString();

        return view('attendance.sessions.index', [
            'sessions' => $sessions,
            'schoolYears' => SchoolYear::query()->orderByDesc('starts_at')->get(),
            'sections' => Section::query()->orderBy('name')->get(),
            'teachers' => Teacher::query()->orderBy('last_name')->orderBy('first_name')->get(),
            'classSchedules' => ClassSchedule::query()->with(['section', 'subject'])->orderBy('day_of_week')->orderBy('starts_at')->get(),
            'statuses' => [AttendanceSession::STATUS_DRAFT, AttendanceSession::STATUS_SUBMITTED],
        ]);
    }

    public function show(AttendanceSession $session): View
    {
        $session->load([
            'classSchedule.schoolYear',
            'classSchedule.section.gradeLevel',
            'classSchedule.subject',
            'classSchedule.teacher',
            'classSchedule.classroom',
            'submittedBy',
            'records.student',
        ]);

        return view('attendance.sessions.show', [
            'session' => $session,
            'recordStatuses' => AttendanceRecord::statuses(),
        ]);
    }
}
