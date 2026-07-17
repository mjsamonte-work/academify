<?php

namespace App\Http\Controllers\Scheduling;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\Section;
use Illuminate\View\View;

class SectionScheduleController extends Controller
{
    public function show(Section $section): View
    {
        $section->load('gradeLevel');

        return view('scheduling.sections.show', [
            'section' => $section,
            'classSchedules' => ClassSchedule::query()
                ->with(['schoolYear', 'subject', 'teacher', 'classroom'])
                ->where('section_id', $section->id)
                ->orderBy('day_of_week')
                ->orderBy('starts_at')
                ->get(),
        ]);
    }
}
