<?php

namespace App\Http\Requests\Administration;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'school_name' => ['required', 'string', 'max:120'],
            'school_code' => ['nullable', 'string', 'max:30'],
            'school_email' => ['nullable', 'email', 'max:120'],
            'school_phone' => ['nullable', 'string', 'max:40'],
            'school_address' => ['nullable', 'string', 'max:500'],
            'active_school_year_id' => ['nullable', Rule::exists('school_years', 'id')],
            'default_grading_period_id' => ['nullable', Rule::exists('grading_periods', 'id')],
            'timezone_label' => ['nullable', 'string', 'max:80'],
            'report_footer_text' => ['nullable', 'string', 'max:240'],
            'report_prepared_by_label' => ['nullable', 'string', 'max:80'],
        ];
    }
}
