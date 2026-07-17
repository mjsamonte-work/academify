<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(): View
    {
        $teacher = request()->user()?->teacher()->first();

        return view('teacher.schedule', [
            'teacher' => $teacher,
            'classSchedules' => $teacher
                ? ClassSchedule::query()
                    ->with(['schoolYear', 'section.gradeLevel', 'subject', 'classroom'])
                    ->where('teacher_id', $teacher->id)
                    ->orderBy('day_of_week')
                    ->orderBy('starts_at')
                    ->get()
                : collect(),
        ]);
    }
}
