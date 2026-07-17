<?php

namespace App\Http\Requests\Grades;

use App\Models\GradingPeriod;
use Illuminate\Validation\Rule;

class UpdateGradingPeriodRequest extends StoreGradingPeriodRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('grades.update') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var GradingPeriod $gradingPeriod */
        $gradingPeriod = $this->route('grading_period');

        return [
            ...parent::rules(),
            'name' => ['required', 'string', 'max:120', Rule::unique('grading_periods', 'name')->where('school_year_id', $this->integer('school_year_id'))->ignore($gradingPeriod)],
        ];
    }
}
