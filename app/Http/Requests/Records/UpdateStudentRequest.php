<?php

namespace App\Http\Requests\Records;

use App\Models\Student;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends StoreStudentRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('students.update') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Student $student */
        $student = $this->route('student');

        return [
            ...parent::rules(),
            'student_number' => ['required', 'string', 'max:40', Rule::unique('students', 'student_number')->ignore($student)],
        ];
    }
}
