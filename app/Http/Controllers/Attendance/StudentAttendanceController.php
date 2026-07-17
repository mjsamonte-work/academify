<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\View\View;

class StudentAttendanceController extends Controller
{
    public function show(Student $student): View
    {
        $student->load(['gradeLevel', 'section']);

        return view('attendance.students.show', [
            'student' => $student,
            'records' => $student->attendanceRecords()
                ->with(['session.classSchedule.schoolYear', 'session.classSchedule.subject', 'session.classSchedule.section'])
                ->latest()
                ->get(),
        ]);
    }
}
