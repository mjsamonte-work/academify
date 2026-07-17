<?php

namespace App\Http\Requests\Enrollment;

use App\Models\Enrollment;
use App\Models\Section;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('enrollments.create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', Rule::exists('students', 'id')],
            'school_year_id' => ['required', Rule::exists('school_years', 'id')],
            'grade_level_id' => ['required', Rule::exists('grade_levels', 'id')],
            'section_id' => ['required', Rule::exists('sections', 'id')],
            'status' => ['required', Rule::in($this->statuses())],
            'enrolled_at' => ['nullable', 'date'],
            'withdrawn_at' => ['nullable', 'date', 'required_if:status,'.Enrollment::STATUS_WITHDRAWN],
            'completed_at' => ['nullable', 'date', 'required_if:status,'.Enrollment::STATUS_COMPLETED],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $this->validateSectionPlacement($validator);
            $this->validateDuplicateEnrollment($validator);
        });
    }

    /**
     * @return array<int, string>
     */
    protected function statuses(): array
    {
        return [
            Enrollment::STATUS_PENDING,
            Enrollment::STATUS_ENROLLED,
            Enrollment::STATUS_WITHDRAWN,
            Enrollment::STATUS_COMPLETED,
        ];
    }

    protected function enrollmentIdToIgnore(): ?int
    {
        return null;
    }

    private function validateSectionPlacement($validator): void
    {
        $gradeLevelId = $this->integer('grade_level_id');
        $sectionId = $this->integer('section_id');

        if (! $gradeLevelId || ! $sectionId) {
            return;
        }

        $valid = Section::query()
            ->whereKey($sectionId)
            ->where('grade_level_id', $gradeLevelId)
            ->exists();

        if (! $valid) {
            $validator->errors()->add('section_id', 'The selected section must belong to the selected grade level.');
        }
    }

    private function validateDuplicateEnrollment($validator): void
    {
        if ($this->string('status')->toString() !== Enrollment::STATUS_ENROLLED) {
            return;
        }

        $studentId = $this->integer('student_id');
        $schoolYearId = $this->integer('school_year_id');

        if (! $studentId || ! $schoolYearId) {
            return;
        }

        $exists = Enrollment::query()
            ->where('student_id', $studentId)
            ->where('school_year_id', $schoolYearId)
            ->where('status', Enrollment::STATUS_ENROLLED)
            ->when($this->enrollmentIdToIgnore(), fn ($query, $id) => $query->whereKeyNot($id))
            ->exists();

        if ($exists) {
            $validator->errors()->add('student_id', 'The selected student is already enrolled for this school year.');
        }
    }
}
