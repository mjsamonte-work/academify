<?php

namespace App\Http\Requests\Scheduling;

use App\Models\ClassSchedule;

class UpdateClassScheduleRequest extends StoreClassScheduleRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('schedules.update') ?? false;
    }

    protected function scheduleIdToIgnore(): ?int
    {
        /** @var ClassSchedule|null $classSchedule */
        $classSchedule = $this->route('class_schedule');

        return $classSchedule?->id;
    }
}
