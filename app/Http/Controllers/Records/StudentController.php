<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Http\Requests\Records\StoreStudentRequest;
use App\Http\Requests\Records\UpdateStudentRequest;
use App\Models\GradeLevel;
use App\Models\Guardian;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = Student::query()
            ->with(['gradeLevel', 'section', 'guardians'])
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('student_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('grade_level_id'), fn ($query) => $query->where('grade_level_id', $request->integer('grade_level_id')))
            ->when($request->filled('section_id'), fn ($query) => $query->where('section_id', $request->integer('section_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('records.students.index', [
            'students' => $students,
            'gradeLevels' => GradeLevel::query()->orderBy('sort_order')->get(),
            'sections' => Section::query()->with('gradeLevel')->orderBy('name')->get(),
            'statuses' => [Student::STATUS_ACTIVE, Student::STATUS_INACTIVE],
        ]);
    }

    public function create(): View
    {
        abort_unless(request()->user()?->can('students.create'), 403);

        return view('records.students.create', $this->formData());
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $student = Student::create($request->validated());

        return redirect()->route('records.students.show', $student)
            ->with('status', 'Student record created.');
    }

    public function show(Student $student): View
    {
        $student->load(['gradeLevel', 'section', 'guardians']);

        return view('records.students.show', [
            'student' => $student,
            'guardians' => Guardian::query()->orderBy('last_name')->orderBy('first_name')->get(),
        ]);
    }

    public function edit(Student $student): View
    {
        abort_unless(request()->user()?->can('students.update'), 403);

        return view('records.students.edit', [
            'student' => $student,
            ...$this->formData(),
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $student->update($request->validated());

        return redirect()->route('records.students.show', $student)
            ->with('status', 'Student record updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'gradeLevels' => GradeLevel::query()->orderBy('sort_order')->get(),
            'sections' => Section::query()->with('gradeLevel')->orderBy('name')->get(),
            'statuses' => [Student::STATUS_ACTIVE, Student::STATUS_INACTIVE],
        ];
    }
}
