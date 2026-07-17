<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademicSetupController extends Controller
{
    /**
     * @return array<string, mixed>
     */
    private function resources(): array
    {
        return [
            'school-years' => [
                'model' => SchoolYear::class,
                'title' => 'School Years',
                'singular' => 'School Year',
                'route' => 'academic.school-years',
                'with' => [],
            ],
            'terms' => [
                'model' => Term::class,
                'title' => 'Terms',
                'singular' => 'Term',
                'route' => 'academic.terms',
                'with' => ['schoolYear'],
            ],
            'grade-levels' => [
                'model' => GradeLevel::class,
                'title' => 'Grade Levels',
                'singular' => 'Grade Level',
                'route' => 'academic.grade-levels',
                'with' => [],
            ],
            'sections' => [
                'model' => Section::class,
                'title' => 'Sections',
                'singular' => 'Section',
                'route' => 'academic.sections',
                'with' => ['gradeLevel'],
            ],
            'subjects' => [
                'model' => Subject::class,
                'title' => 'Subjects',
                'singular' => 'Subject',
                'route' => 'academic.subjects',
                'with' => [],
            ],
            'classrooms' => [
                'model' => Classroom::class,
                'title' => 'Classrooms',
                'singular' => 'Classroom',
                'route' => 'academic.classrooms',
                'with' => [],
            ],
        ];
    }

    public function index(Request $request, string $resource): View
    {
        $config = $this->resourceConfig($resource);
        /** @var class-string<Model> $model */
        $model = $config['model'];

        $records = $model::query()
            ->with($config['with'])
            ->when($request->string('search')->isNotEmpty(), function (Builder $query) use ($request): void {
                $search = $request->string('search')->toString();

                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%");

                    if ($this->queryHasColumn($query, 'code')) {
                        $query->orWhere('code', 'like', "%{$search}%");
                    }
                });
            })
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')->toString()))
            ->orderBy($this->orderColumn($resource))
            ->paginate(10)
            ->withQueryString();

        return view('academic.setup.index', [
            'config' => $config,
            'resource' => $resource,
            'records' => $records,
            'statuses' => $this->statuses(),
        ]);
    }

    public function create(string $resource): View
    {
        abort_unless(request()->user()?->can('academic_setup.create'), 403);

        return view('academic.setup.create', [
            'config' => $this->resourceConfig($resource),
            'resource' => $resource,
            'statuses' => $this->statuses(),
            'options' => $this->options(),
        ]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        abort_unless($request->user()?->can('academic_setup.create'), 403);

        $config = $this->resourceConfig($resource);
        $validated = $this->validated($request, $resource);

        $this->beforeSave($resource, $validated);

        /** @var class-string<Model> $model */
        $model = $config['model'];
        $record = $model::create($validated);

        return redirect()->route($config['route'].'.edit', $record)
            ->with('status', $config['singular'].' created.');
    }

    public function edit(int|string $record, string $resource): View
    {
        abort_unless(request()->user()?->can('academic_setup.update'), 403);

        $config = $this->resourceConfig($resource);

        return view('academic.setup.edit', [
            'config' => $config,
            'resource' => $resource,
            'record' => $this->findRecord($config, $record),
            'statuses' => $this->statuses(),
            'options' => $this->options(),
        ]);
    }

    public function update(Request $request, int|string $record, string $resource): RedirectResponse
    {
        abort_unless($request->user()?->can('academic_setup.update'), 403);

        $config = $this->resourceConfig($resource);
        $model = $this->findRecord($config, $record);
        $validated = $this->validated($request, $resource, $model);

        $this->beforeSave($resource, $validated, $model);
        $model->update($validated);

        return redirect()->route($config['route'].'.edit', $model)
            ->with('status', $config['singular'].' updated.');
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function findRecord(array $config, int|string $id): Model
    {
        /** @var class-string<Model> $model */
        $model = $config['model'];

        return $model::query()->with($config['with'])->findOrFail($id);
    }

    /**
     * @return array<string, mixed>
     */
    private function resourceConfig(string $resource): array
    {
        abort_unless(array_key_exists($resource, $this->resources()), 404);

        return $this->resources()[$resource];
    }

    /**
     * @return array<int, string>
     */
    private function statuses(): array
    {
        return [SchoolYear::STATUS_ACTIVE, SchoolYear::STATUS_INACTIVE];
    }

    /**
     * @return array<string, mixed>
     */
    private function options(): array
    {
        return [
            'schoolYears' => SchoolYear::query()->orderByDesc('is_active')->orderBy('starts_at')->get(),
            'gradeLevels' => GradeLevel::query()->orderBy('sort_order')->orderBy('name')->get(),
        ];
    }

    private function orderColumn(string $resource): string
    {
        return match ($resource) {
            'school-years' => 'starts_at',
            'terms', 'grade-levels' => 'sort_order',
            default => 'name',
        };
    }

    /**
     * @param  Builder<Model>  $query
     */
    private function queryHasColumn(Builder $query, string $column): bool
    {
        return in_array($column, $query->getModel()->getFillable(), true);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, string $resource, ?Model $record = null): array
    {
        $rules = match ($resource) {
            'school-years' => $this->schoolYearRules($record),
            'terms' => $this->termRules($record),
            'grade-levels' => $this->gradeLevelRules($record),
            'sections' => $this->sectionRules($record),
            'subjects' => $this->subjectRules($record),
            'classrooms' => $this->classroomRules($record),
            default => abort(404),
        };

        $validator = Validator::make($request->all(), $rules);

        if ($resource === 'terms') {
            $validator->after(function ($validator) use ($request): void {
                $schoolYear = SchoolYear::find($request->integer('school_year_id'));

                if (! $schoolYear || ! $request->filled(['starts_at', 'ends_at'])) {
                    return;
                }

                $startsAt = Carbon::parse($request->input('starts_at'));
                $endsAt = Carbon::parse($request->input('ends_at'));

                if ($startsAt->lt($schoolYear->starts_at) || $endsAt->gt($schoolYear->ends_at)) {
                    $validator->errors()->add('starts_at', 'Term dates must stay within the selected school year.');
                }
            });
        }

        return $validator->validate();
    }

    /**
     * @return array<string, mixed>
     */
    private function schoolYearRules(?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('school_years', 'name')->ignore($record)],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'is_active' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in($this->statuses())],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function termRules(?Model $record): array
    {
        return [
            'school_year_id' => ['required', Rule::exists('school_years', 'id')],
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('terms', 'name')
                    ->where('school_year_id', request()->integer('school_year_id'))
                    ->ignore($record),
            ],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'status' => ['required', Rule::in($this->statuses())],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function gradeLevelRules(?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('grade_levels', 'name')->ignore($record)],
            'code' => ['required', 'string', 'max:40', Rule::unique('grade_levels', 'code')->ignore($record)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'status' => ['required', Rule::in($this->statuses())],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function sectionRules(?Model $record): array
    {
        return [
            'grade_level_id' => ['required', Rule::exists('grade_levels', 'id')],
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('sections', 'name')
                    ->where('grade_level_id', request()->integer('grade_level_id'))
                    ->ignore($record),
            ],
            'code' => ['required', 'string', 'max:40', Rule::unique('sections', 'code')->ignore($record)],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:999'],
            'status' => ['required', Rule::in($this->statuses())],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function subjectRules(?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('subjects', 'name')->ignore($record)],
            'code' => ['required', 'string', 'max:40', Rule::unique('subjects', 'code')->ignore($record)],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in($this->statuses())],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function classroomRules(?Model $record): array
    {
        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('classrooms', 'name')->ignore($record)],
            'code' => ['required', 'string', 'max:40', Rule::unique('classrooms', 'code')->ignore($record)],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:999'],
            'location' => ['nullable', 'string', 'max:120'],
            'status' => ['required', Rule::in($this->statuses())],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function beforeSave(string $resource, array &$validated, ?Model $record = null): void
    {
        if ($resource !== 'school-years') {
            return;
        }

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        if (! $validated['is_active']) {
            return;
        }

        SchoolYear::query()
            ->when($record, fn (Builder $query) => $query->whereKeyNot($record->getKey()))
            ->update(['is_active' => false]);
    }
}
