<?php

namespace App\Http\Requests\Grades;

use App\Models\Assessment;
use App\Models\Enrollment;
use App\Models\StudentGrade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveStudentGradesRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        /** @var Assessment|null $assessment */
        $assessment = $this->route('assessment');

        if (! $user || ! $assessment) {
            return false;
        }

        if ($user->can('grades.view')) {
            return true;
        }

        return $user->can('grades.view_own')
            && $user->teacher?->id === $assessment->classSchedule?->teacher_id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Assessment $assessment */
        $assessment = $this->route('assessment');

        return [
            'grade_status' => ['required', Rule::in([StudentGrade::STATUS_DRAFT, StudentGrade::STATUS_SUBMITTED, StudentGrade::STATUS_PUBLISHED])],
            'grades' => ['required', 'array', 'min:1'],
            'grades.*.student_id' => ['required', Rule::exists('students', 'id')],
            'grades.*.score' => ['nullable', 'numeric', 'min:0', 'max:'.$assessment->max_score],
            'grades.*.remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var Assessment|null $assessment */
            $assessment = $this->route('assessment');

            if (! $assessment) {
                return;
            }

            $assessment->loadMissing('classSchedule');
            $studentIds = collect($this->input('grades', []))
                ->pluck('student_id')
                ->filter()
                ->map(fn ($id) => (int) $id);

            if ($studentIds->duplicates()->isNotEmpty()) {
                $validator->errors()->add('grades', 'Each student may only appear once per assessment.');
            }

            $validStudentIds = Enrollment::query()
                ->where('school_year_id', $assessment->classSchedule->school_year_id)
                ->where('section_id', $assessment->classSchedule->section_id)
                ->where('status', Enrollment::STATUS_ENROLLED)
                ->whereIn('student_id', $studentIds->all())
                ->pluck('student_id');

            if ($studentIds->diff($validStudentIds)->isNotEmpty()) {
                $validator->errors()->add('grades', 'Grade records may only include students enrolled in the scheduled section.');
            }
        });
    }
}
