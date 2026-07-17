<?php

namespace App\Http\Requests\Attendance;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ClassSchedule;
use App\Models\Enrollment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        if ($user->can('attendance.view')) {
            return true;
        }

        /** @var ClassSchedule|null $classSchedule */
        $classSchedule = $this->route('classSchedule');

        return $user->can('attendance.view_own')
            && $user->teacher?->id === $classSchedule?->teacher_id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'attendance_date' => ['required', 'date'],
            'session_status' => ['required', Rule::in([AttendanceSession::STATUS_DRAFT, AttendanceSession::STATUS_SUBMITTED])],
            'notes' => ['nullable', 'string', 'max:1000'],
            'records' => ['required', 'array', 'min:1'],
            'records.*.student_id' => ['required', Rule::exists('students', 'id')],
            'records.*.status' => ['required', Rule::in(AttendanceRecord::statuses())],
            'records.*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var ClassSchedule|null $classSchedule */
            $classSchedule = $this->route('classSchedule');

            if (! $classSchedule) {
                return;
            }

            $recordInput = is_array($this->input('records')) ? $this->input('records') : [];
            $studentIds = collect($recordInput)
                ->pluck('student_id')
                ->filter()
                ->map(fn ($id) => (int) $id);

            if ($studentIds->duplicates()->isNotEmpty()) {
                $validator->errors()->add('records', 'Each student may only appear once per attendance session.');
            }

            $validStudentIds = Enrollment::query()
                ->where('school_year_id', $classSchedule->school_year_id)
                ->where('section_id', $classSchedule->section_id)
                ->where('status', Enrollment::STATUS_ENROLLED)
                ->whereIn('student_id', $studentIds->all())
                ->pluck('student_id');

            if ($studentIds->diff($validStudentIds)->isNotEmpty()) {
                $validator->errors()->add('records', 'Attendance records may only include students enrolled in the scheduled section.');
            }
        });
    }
}
