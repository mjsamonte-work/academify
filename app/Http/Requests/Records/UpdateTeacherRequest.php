<?php

namespace App\Http\Requests\Records;

use App\Models\Teacher;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends StoreTeacherRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('teachers.update') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Teacher $teacher */
        $teacher = $this->route('teacher');

        return [
            ...parent::rules(),
            'employee_number' => ['required', 'string', 'max:40', Rule::unique('teachers', 'employee_number')->ignore($teacher)],
            'user_id' => ['nullable', Rule::exists('users', 'id'), Rule::unique('teachers', 'user_id')->ignore($teacher)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('teachers', 'email')->ignore($teacher)],
        ];
    }
}
