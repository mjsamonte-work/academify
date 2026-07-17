<?php

namespace App\Http\Requests\Enrollment;

use App\Models\Enrollment;

class UpdateEnrollmentRequest extends StoreEnrollmentRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('enrollments.update') ?? false;
    }

    protected function enrollmentIdToIgnore(): ?int
    {
        /** @var Enrollment|null $enrollment */
        $enrollment = $this->route('enrollment');

        return $enrollment?->id;
    }
}
