<?php

namespace App\Http\Requests\Grades;

use App\Models\GradingPeriod;
use App\Models\SchoolYear;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class StoreGradingPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('grades.create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'school_year_id' => ['required', Rule::exists('school_years', 'id')],
            'name' => ['required', 'string', 'max:120', Rule::unique('grading_periods', 'name')->where('school_year_id', $this->integer('school_year_id'))],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'status' => ['required', Rule::in([GradingPeriod::STATUS_ACTIVE, GradingPeriod::STATUS_INACTIVE])],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $schoolYear = SchoolYear::find($this->integer('school_year_id'));

            if (! $schoolYear || ! $this->filled(['starts_at', 'ends_at'])) {
                return;
            }

            $startsAt = Carbon::parse($this->input('starts_at'));
            $endsAt = Carbon::parse($this->input('ends_at'));

            if ($startsAt->lt($schoolYear->starts_at) || $endsAt->gt($schoolYear->ends_at)) {
                $validator->errors()->add('starts_at', 'Grading period dates must stay within the selected school year.');
            }
        });
    }
}
