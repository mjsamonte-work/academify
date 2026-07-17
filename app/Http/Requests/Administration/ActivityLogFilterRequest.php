<?php

namespace App\Http\Requests\Administration;

use App\Models\ActivityLog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityLogFilterRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'module' => ['nullable', Rule::in(ActivityLog::modules())],
            'action' => ['nullable', Rule::in(ActivityLog::actions())],
            'user_id' => ['nullable', Rule::exists('users', 'id')],
            'search' => ['nullable', 'string', 'max:120'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ];
    }
}
