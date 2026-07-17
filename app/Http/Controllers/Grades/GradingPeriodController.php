<?php

namespace App\Http\Controllers\Grades;

use App\Http\Controllers\Controller;
use App\Http\Requests\Grades\StoreGradingPeriodRequest;
use App\Http\Requests\Grades\UpdateGradingPeriodRequest;
use App\Models\GradingPeriod;
use App\Models\SchoolYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradingPeriodController extends Controller
{
    public function index(Request $request): View
    {
        $gradingPeriods = GradingPeriod::query()
            ->with('schoolYear')
            ->when($request->filled('school_year_id'), fn ($query) => $query->where('school_year_id', $request->integer('school_year_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->orderBy('sort_order')
            ->paginate(10)
            ->withQueryString();

        return view('grades.grading-periods.index', [
            'gradingPeriods' => $gradingPeriods,
            ...$this->formData(),
        ]);
    }

    public function create(): View
    {
        abort_unless(request()->user()?->can('grades.create'), 403);

        return view('grades.grading-periods.create', $this->formData());
    }

    public function store(StoreGradingPeriodRequest $request): RedirectResponse
    {
        $gradingPeriod = GradingPeriod::create($request->validated());

        return redirect()->route('grades.grading-periods.show', $gradingPeriod)
            ->with('status', 'Grading period created.');
    }

    public function show(GradingPeriod $gradingPeriod): View
    {
        $gradingPeriod->load(['schoolYear', 'assessments.classSchedule.subject', 'assessments.classSchedule.section']);

        return view('grades.grading-periods.show', compact('gradingPeriod'));
    }

    public function edit(GradingPeriod $gradingPeriod): View
    {
        abort_unless(request()->user()?->can('grades.update'), 403);

        return view('grades.grading-periods.edit', [
            'gradingPeriod' => $gradingPeriod,
            ...$this->formData(),
        ]);
    }

    public function update(UpdateGradingPeriodRequest $request, GradingPeriod $gradingPeriod): RedirectResponse
    {
        $gradingPeriod->update($request->validated());

        return redirect()->route('grades.grading-periods.show', $gradingPeriod)
            ->with('status', 'Grading period updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'schoolYears' => SchoolYear::query()->orderByDesc('starts_at')->get(),
            'statuses' => [GradingPeriod::STATUS_ACTIVE, GradingPeriod::STATUS_INACTIVE],
        ];
    }
}
