<?php

namespace App\Http\Requests\Records;

use App\Models\Guardian;
use Illuminate\Validation\Rule;

class UpdateGuardianRequest extends StoreGuardianRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('guardians.update') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Guardian $guardian */
        $guardian = $this->route('guardian');

        return [
            ...parent::rules(),
            'email' => ['nullable', 'email', 'max:255', Rule::unique('guardians', 'email')->ignore($guardian)],
        ];
    }
}
