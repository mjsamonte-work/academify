<?php

namespace App\Http\Controllers\Enrollment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Enrollment\StoreEnrollmentRequest;
use App\Http\Requests\Enrollment\UpdateEnrollmentRequest;
use App\Models\Enrollment;
use App\Models\GradeLevel;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function index(Request $request): View
    {
        $enrollments = Enrollment::query()
            ->with(['student', 'schoolYear', 'gradeLevel', 'section'])
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->whereHas('student', function ($query) use ($search): void {
                    $query->where('student_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('school_year_id'), fn ($query) => $query->where('school_year_id', $request->integer('school_year_id')))
            ->when($request->filled('grade_level_id'), fn ($query) => $query->where('grade_level_id', $request->integer('grade_level_id')))
            ->when($request->filled('section_id'), fn ($query) => $query->where('section_id', $request->integer('section_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('enrollment.enrollments.index', [
            'enrollments' => $enrollments,
            ...$this->lookupData(),
        ]);
    }

    public function create(): View
    {
        abort_unless(request()->user()?->can('enrollments.create'), 403);

        return view('enrollment.enrollments.create', $this->formData());
    }

    public function store(StoreEnrollmentRequest $request): RedirectResponse
    {
        $enrollment = Enrollment::create($request->validated());

        return redirect()->route('enrollment.enrollments.show', $enrollment)
            ->with('status', 'Enrollment record created.');
    }

    public function show(Enrollment $enrollment): View
    {
        $enrollment->load(['student', 'schoolYear', 'gradeLevel', 'section']);

        return view('enrollment.enrollments.show', [
            'enrollment' => $enrollment,
            'history' => $enrollment->student->enrollments()
                ->with(['schoolYear', 'gradeLevel', 'section'])
                ->latest()
                ->get(),
        ]);
    }

    public function edit(Enrollment $enrollment): View
    {
        abort_unless(request()->user()?->can('enrollments.update'), 403);

        return view('enrollment.enrollments.edit', [
            'enrollment' => $enrollment,
            ...$this->formData(),
        ]);
    }

    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment): RedirectResponse
    {
        $enrollment->update($request->validated());

        return redirect()->route('enrollment.enrollments.show', $enrollment)
            ->with('status', 'Enrollment record updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'students' => Student::query()->orderBy('last_name')->orderBy('first_name')->get(),
            ...$this->lookupData(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function lookupData(): array
    {
        return [
            'schoolYears' => SchoolYear::query()->orderByDesc('starts_at')->get(),
            'gradeLevels' => GradeLevel::query()->orderBy('sort_order')->get(),
            'sections' => Section::query()->with('gradeLevel')->orderBy('name')->get(),
            'statuses' => [
                Enrollment::STATUS_PENDING,
                Enrollment::STATUS_ENROLLED,
                Enrollment::STATUS_WITHDRAWN,
                Enrollment::STATUS_COMPLETED,
            ],
        ];
    }
}
