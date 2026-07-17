<?php

namespace App\Http\Requests\Grades;

class UpdateAssessmentRequest extends StoreAssessmentRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('grades.update') ?? false;
    }
}
