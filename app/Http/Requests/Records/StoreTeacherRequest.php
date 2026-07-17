<?php

namespace App\Http\Requests\Records;

use App\Models\Teacher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('teachers.create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'employee_number' => ['required', 'string', 'max:40', 'unique:teachers,employee_number'],
            'user_id' => ['nullable', Rule::exists('users', 'id'), 'unique:teachers,user_id'],
            'first_name' => ['required', 'string', 'max:120'],
            'middle_name' => ['nullable', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'preferred_name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255', 'unique:teachers,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'department' => ['nullable', 'string', 'max:120'],
            'employment_type' => ['nullable', Rule::in($this->employmentTypes())],
            'hired_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in([Teacher::STATUS_ACTIVE, Teacher::STATUS_INACTIVE])],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => [Rule::exists('subjects', 'id')],
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function employmentTypes(): array
    {
        return [
            Teacher::EMPLOYMENT_FULL_TIME,
            Teacher::EMPLOYMENT_PART_TIME,
            Teacher::EMPLOYMENT_CONTRACT,
        ];
    }
}
