<?php

namespace App\Http\Requests\Records;

use App\Models\Guardian;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuardianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('guardians.create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120'],
            'middle_name' => ['nullable', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255', 'unique:guardians,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'alternate_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'occupation' => ['nullable', 'string', 'max:120'],
            'status' => ['required', Rule::in([Guardian::STATUS_ACTIVE, Guardian::STATUS_INACTIVE])],
        ];
    }
}
