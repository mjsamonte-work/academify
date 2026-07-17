<?php

namespace App\Http\Requests\Grades;

use App\Models\Assessment;
use App\Models\ClassSchedule;
use App\Models\GradingPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAssessmentRequest extends FormRequest
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
            'class_schedule_id' => ['required', Rule::exists('class_schedules', 'id')],
            'grading_period_id' => ['required', Rule::exists('grading_periods', 'id')],
            'title' => ['required', 'string', 'max:160'],
            'assessment_type' => ['nullable', 'string', 'max:60'],
            'max_score' => ['required', 'numeric', 'gt:0', 'max:999999'],
            'weight' => ['required', 'numeric', 'min:0', 'max:100'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in([Assessment::STATUS_DRAFT, Assessment::STATUS_SUBMITTED, Assessment::STATUS_PUBLISHED])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $classSchedule = ClassSchedule::find($this->integer('class_schedule_id'));
            $gradingPeriod = GradingPeriod::find($this->integer('grading_period_id'));

            if (! $classSchedule || ! $gradingPeriod) {
                return;
            }

            if ($classSchedule->school_year_id !== $gradingPeriod->school_year_id) {
                $validator->errors()->add('grading_period_id', 'The grading period must belong to the same school year as the selected class schedule.');
            }
        });
    }
}
