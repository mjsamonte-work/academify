<?php

namespace App\Http\Requests\Scheduling;

use App\Models\ClassSchedule;
use App\Models\Teacher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('schedules.create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'school_year_id' => ['required', Rule::exists('school_years', 'id')],
            'section_id' => ['required', Rule::exists('sections', 'id')],
            'subject_id' => ['required', Rule::exists('subjects', 'id')],
            'teacher_id' => ['required', Rule::exists('teachers', 'id')],
            'classroom_id' => ['nullable', Rule::exists('classrooms', 'id')],
            'day_of_week' => ['required', Rule::in(ClassSchedule::days())],
            'starts_at' => ['required', 'date_format:H:i'],
            'ends_at' => ['required', 'date_format:H:i', 'after:starts_at'],
            'status' => ['required', Rule::in([ClassSchedule::STATUS_ACTIVE, ClassSchedule::STATUS_INACTIVE])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $this->validateTeacherSubject($validator);

            if ($this->string('status')->toString() !== ClassSchedule::STATUS_ACTIVE) {
                return;
            }

            $this->validateOverlap($validator, 'teacher_id', 'teacher_id', 'The selected teacher has an overlapping schedule.');
            $this->validateOverlap($validator, 'section_id', 'section_id', 'The selected section has an overlapping schedule.');

            if ($this->integer('classroom_id')) {
                $this->validateOverlap($validator, 'classroom_id', 'classroom_id', 'The selected classroom has an overlapping schedule.');
            }
        });
    }

    protected function scheduleIdToIgnore(): ?int
    {
        return null;
    }

    private function validateTeacherSubject($validator): void
    {
        $teacherId = $this->integer('teacher_id');
        $subjectId = $this->integer('subject_id');

        if (! $teacherId || ! $subjectId) {
            return;
        }

        $assigned = Teacher::query()
            ->whereKey($teacherId)
            ->whereHas('subjects', fn ($query) => $query->whereKey($subjectId))
            ->exists();

        if (! $assigned) {
            $validator->errors()->add('teacher_id', 'The selected teacher must be assigned to the selected subject.');
        }
    }

    private function validateOverlap($validator, string $field, string $column, string $message): void
    {
        $schoolYearId = $this->integer('school_year_id');
        $dayOfWeek = $this->string('day_of_week')->toString();
        $startsAt = $this->string('starts_at')->toString();
        $endsAt = $this->string('ends_at')->toString();
        $value = $this->integer($field);

        if (! $schoolYearId || ! $dayOfWeek || ! $startsAt || ! $endsAt || ! $value) {
            return;
        }

        $exists = ClassSchedule::query()
            ->where('school_year_id', $schoolYearId)
            ->where('day_of_week', $dayOfWeek)
            ->where('status', ClassSchedule::STATUS_ACTIVE)
            ->where($column, $value)
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->when($this->scheduleIdToIgnore(), fn ($query, $id) => $query->whereKeyNot($id))
            ->exists();

        if ($exists) {
            $validator->errors()->add($field, $message);
        }
    }
}
