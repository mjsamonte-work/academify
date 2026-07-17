<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Http\Requests\Records\StoreTeacherRequest;
use App\Http\Requests\Records\UpdateTeacherRequest;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $teachers = Teacher::query()
            ->with(['subjects', 'user'])
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('employee_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('subject_id'), fn ($query) => $query->whereHas('subjects', fn ($query) => $query->whereKey($request->integer('subject_id'))))
            ->when($request->filled('account'), function ($query) use ($request): void {
                match ($request->string('account')->toString()) {
                    'linked' => $query->whereNotNull('user_id'),
                    'unlinked' => $query->whereNull('user_id'),
                    default => null,
                };
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('records.teachers.index', [
            'teachers' => $teachers,
            'subjects' => Subject::query()->orderBy('name')->get(),
            'statuses' => [Teacher::STATUS_ACTIVE, Teacher::STATUS_INACTIVE],
        ]);
    }

    public function create(): View
    {
        abort_unless(request()->user()?->can('teachers.create'), 403);

        return view('records.teachers.create', $this->formData());
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        [$attributes, $subjectIds] = $this->validatedPayload($request->validated());

        $teacher = Teacher::create($attributes);
        $teacher->subjects()->sync($subjectIds);
        $this->assignTeacherRole($teacher);

        return redirect()->route('records.teachers.show', $teacher)
            ->with('status', 'Teacher record created.');
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load(['subjects', 'user']);

        return view('records.teachers.show', [
            'teacher' => $teacher,
        ]);
    }

    public function edit(Teacher $teacher): View
    {
        abort_unless(request()->user()?->can('teachers.update'), 403);

        return view('records.teachers.edit', [
            'teacher' => $teacher->load('subjects'),
            ...$this->formData($teacher),
        ]);
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        [$attributes, $subjectIds] = $this->validatedPayload($request->validated());

        $teacher->update($attributes);
        $teacher->subjects()->sync($subjectIds);
        $this->assignTeacherRole($teacher);

        return redirect()->route('records.teachers.show', $teacher)
            ->with('status', 'Teacher record updated.');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{array<string, mixed>, array<int, int>}
     */
    private function validatedPayload(array $validated): array
    {
        $subjectIds = array_map('intval', Arr::pull($validated, 'subject_ids', []));
        $validated['user_id'] = $validated['user_id'] ?? null;

        return [$validated, $subjectIds];
    }

    private function assignTeacherRole(Teacher $teacher): void
    {
        if (! $teacher->user_id) {
            return;
        }

        $teacher->user()->first()?->assignRole('Teacher');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(?Teacher $teacher = null): array
    {
        return [
            'subjects' => Subject::query()->orderBy('name')->get(),
            'users' => User::query()
                ->where(function ($query) use ($teacher): void {
                    $query->whereDoesntHave('teacher')
                        ->when($teacher?->user_id, fn ($query) => $query->orWhereKey($teacher->user_id));
                })
                ->orderBy('name')
                ->get(),
            'statuses' => [Teacher::STATUS_ACTIVE, Teacher::STATUS_INACTIVE],
            'employmentTypes' => [
                Teacher::EMPLOYMENT_FULL_TIME,
                Teacher::EMPLOYMENT_PART_TIME,
                Teacher::EMPLOYMENT_CONTRACT,
            ],
        ];
    }
}
