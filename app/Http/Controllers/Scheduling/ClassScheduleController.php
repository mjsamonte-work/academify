<?php

namespace App\Http\Controllers\Scheduling;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduling\StoreClassScheduleRequest;
use App\Http\Requests\Scheduling\UpdateClassScheduleRequest;
use App\Models\Classroom;
use App\Models\ClassSchedule;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $classSchedules = ClassSchedule::query()
            ->with(['schoolYear', 'section.gradeLevel', 'subject', 'teacher', 'classroom'])
            ->when($request->filled('school_year_id'), fn ($query) => $query->where('school_year_id', $request->integer('school_year_id')))
            ->when($request->filled('section_id'), fn ($query) => $query->where('section_id', $request->integer('section_id')))
            ->when($request->filled('teacher_id'), fn ($query) => $query->where('teacher_id', $request->integer('teacher_id')))
            ->when($request->filled('subject_id'), fn ($query) => $query->where('subject_id', $request->integer('subject_id')))
            ->when($request->filled('classroom_id'), fn ($query) => $query->where('classroom_id', $request->integer('classroom_id')))
            ->when($request->filled('day_of_week'), fn ($query) => $query->where('day_of_week', $request->string('day_of_week')->toString()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->orderBy('day_of_week')
            ->orderBy('starts_at')
            ->paginate(10)
            ->withQueryString();

        return view('scheduling.class-schedules.index', [
            'classSchedules' => $classSchedules,
            ...$this->lookupData(),
        ]);
    }

    public function create(): View
    {
        abort_unless(request()->user()?->can('schedules.create'), 403);

        return view('scheduling.class-schedules.create', $this->lookupData());
    }

    public function store(StoreClassScheduleRequest $request): RedirectResponse
    {
        $classSchedule = ClassSchedule::create($request->validated());

        return redirect()->route('scheduling.class-schedules.show', $classSchedule)
            ->with('status', 'Class schedule created.');
    }

    public function show(ClassSchedule $classSchedule): View
    {
        $classSchedule->load(['schoolYear', 'section.gradeLevel', 'subject', 'teacher.user', 'classroom']);

        return view('scheduling.class-schedules.show', [
            'classSchedule' => $classSchedule,
        ]);
    }

    public function edit(ClassSchedule $classSchedule): View
    {
        abort_unless(request()->user()?->can('schedules.update'), 403);

        return view('scheduling.class-schedules.edit', [
            'classSchedule' => $classSchedule,
            ...$this->lookupData(),
        ]);
    }

    public function update(UpdateClassScheduleRequest $request, ClassSchedule $classSchedule): RedirectResponse
    {
        $classSchedule->update($request->validated());

        return redirect()->route('scheduling.class-schedules.show', $classSchedule)
            ->with('status', 'Class schedule updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function lookupData(): array
    {
        return [
            'schoolYears' => SchoolYear::query()->orderByDesc('starts_at')->get(),
            'sections' => Section::query()->with('gradeLevel')->orderBy('name')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
            'teachers' => Teacher::query()->with('subjects')->orderBy('last_name')->orderBy('first_name')->get(),
            'classrooms' => Classroom::query()->orderBy('name')->get(),
            'days' => ClassSchedule::days(),
            'statuses' => [ClassSchedule::STATUS_ACTIVE, ClassSchedule::STATUS_INACTIVE],
        ];
    }
}
