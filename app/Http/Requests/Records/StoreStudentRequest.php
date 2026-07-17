<?php

namespace App\Http\Requests\Records;

use App\Models\Section;
use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('students.create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student_number' => ['required', 'string', 'max:40', 'unique:students,student_number'],
            'first_name' => ['required', 'string', 'max:120'],
            'middle_name' => ['nullable', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'preferred_name' => ['nullable', 'string', 'max:120'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'grade_level_id' => ['nullable', Rule::exists('grade_levels', 'id')],
            'section_id' => ['nullable', Rule::exists('sections', 'id')],
            'status' => ['required', Rule::in([Student::STATUS_ACTIVE, Student::STATUS_INACTIVE])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
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
        });
    }
}
