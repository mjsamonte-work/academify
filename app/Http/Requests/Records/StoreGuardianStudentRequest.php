<?php

namespace App\Http\Requests\Records;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuardianStudentRequest extends FormRequest
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
        return [
            'guardian_id' => ['required', Rule::exists('guardians', 'id')],
            'relationship' => ['required', 'string', 'max:80'],
            'is_primary_contact' => ['nullable', 'boolean'],
            'can_pick_up' => ['nullable', 'boolean'],
            'receives_notifications' => ['nullable', 'boolean'],
        ];
    }
}
