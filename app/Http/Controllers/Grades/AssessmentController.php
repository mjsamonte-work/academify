<?php

namespace App\Http\Controllers\Grades;

use App\Http\Controllers\Controller;
use App\Http\Requests\Grades\StoreAssessmentRequest;
use App\Http\Requests\Grades\UpdateAssessmentRequest;
use App\Models\Assessment;
use App\Models\ClassSchedule;
use App\Models\GradingPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(Request $request): View
    {
        $assessments = Assessment::query()
            ->with(['classSchedule.schoolYear', 'classSchedule.section', 'classSchedule.subject', 'classSchedule.teacher', 'gradingPeriod'])
            ->when($request->filled('grading_period_id'), fn ($query) => $query->where('grading_period_id', $request->integer('grading_period_id')))
            ->when($request->filled('class_schedule_id'), fn ($query) => $query->where('class_schedule_id', $request->integer('class_schedule_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('grades.assessments.index', [
            'assessments' => $assessments,
            ...$this->formData(),
        ]);
    }

    public function create(): View
    {
        abort_unless(request()->user()?->can('grades.create'), 403);

        return view('grades.assessments.create', $this->formData());
    }

    public function store(StoreAssessmentRequest $request): RedirectResponse
    {
        $assessment = Assessment::create($request->validated());

        return redirect()->route('grades.assessments.show', $assessment)
            ->with('status', 'Assessment created.');
    }

    public function show(Assessment $assessment): View
    {
        $assessment->load(['classSchedule.schoolYear', 'classSchedule.section', 'classSchedule.subject', 'classSchedule.teacher', 'gradingPeriod']);

        return view('grades.assessments.show', compact('assessment'));
    }

    public function edit(Assessment $assessment): View
    {
        abort_unless(request()->user()?->can('grades.update'), 403);

        return view('grades.assessments.edit', [
            'assessment' => $assessment,
            ...$this->formData(),
        ]);
    }

    public function update(UpdateAssessmentRequest $request, Assessment $assessment): RedirectResponse
    {
        $assessment->update($request->validated());

        return redirect()->route('grades.assessments.show', $assessment)
            ->with('status', 'Assessment updated.');
    }

    public function records(Assessment $assessment): View
    {
        $assessment->load(['classSchedule.section', 'classSchedule.subject', 'gradingPeriod', 'studentGrades.student', 'studentGrades.submittedBy']);

        return view('grades.assessments.records', compact('assessment'));
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'classSchedules' => ClassSchedule::query()->with(['schoolYear', 'section', 'subject', 'teacher'])->orderBy('day_of_week')->orderBy('starts_at')->get(),
            'gradingPeriods' => GradingPeriod::query()->with('schoolYear')->orderBy('sort_order')->get(),
            'statuses' => [Assessment::STATUS_DRAFT, Assessment::STATUS_SUBMITTED, Assessment::STATUS_PUBLISHED],
        ];
    }
}
