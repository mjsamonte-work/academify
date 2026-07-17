<?php

namespace App\Reports;

use App\Models\AttendanceRecord;
use App\Models\ClassSchedule;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\StudentGrade;

class AcademifyReport
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array{title: string, headings: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    public function build(string $report, array $filters): array
    {
        return match ($report) {
            'students' => $this->students($filters),
            'enrollments' => $this->enrollments($filters),
            'attendance' => $this->attendance($filters),
            'grades' => $this->grades($filters),
            'teacher-schedules' => $this->teacherSchedules($filters),
            default => abort(404),
        };
    }

    private function students(array $filters): array
    {
        $rows = Student::query()
            ->with(['gradeLevel', 'section', 'guardians'])
            ->when($filters['grade_level_id'] ?? null, fn ($query, $id) => $query->where('grade_level_id', $id))
            ->when($filters['section_id'] ?? null, fn ($query, $id) => $query->where('section_id', $id))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderBy('last_name')
            ->get()
            ->map(fn (Student $student): array => [
                $student->student_number,
                $student->fullName(),
                $student->gradeLevel?->name ?? 'Not set',
                $student->section?->code ?? 'Not set',
                str($student->status)->headline()->toString(),
                $student->guardians->count(),
                $student->email,
                $student->phone,
            ])
            ->all();

        return ['title' => 'Student Master List', 'headings' => ['Student No.', 'Name', 'Grade', 'Section', 'Status', 'Guardians', 'Email', 'Phone'], 'rows' => $rows];
    }

    private function enrollments(array $filters): array
    {
        $rows = Enrollment::query()
            ->with(['student', 'schoolYear', 'gradeLevel', 'section'])
            ->when($filters['school_year_id'] ?? null, fn ($query, $id) => $query->where('school_year_id', $id))
            ->when($filters['grade_level_id'] ?? null, fn ($query, $id) => $query->where('grade_level_id', $id))
            ->when($filters['section_id'] ?? null, fn ($query, $id) => $query->where('section_id', $id))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->get()
            ->map(fn (Enrollment $enrollment): array => [
                $enrollment->student->student_number,
                $enrollment->student->fullName(),
                $enrollment->schoolYear->name,
                $enrollment->gradeLevel->name,
                $enrollment->section->code,
                str($enrollment->status)->headline()->toString(),
                $enrollment->enrolled_at?->toDateString(),
                $enrollment->withdrawn_at?->toDateString(),
                $enrollment->completed_at?->toDateString(),
            ])
            ->all();

        return ['title' => 'Enrollment Report', 'headings' => ['Student No.', 'Name', 'School Year', 'Grade', 'Section', 'Status', 'Enrolled', 'Withdrawn', 'Completed'], 'rows' => $rows];
    }

    private function attendance(array $filters): array
    {
        $records = AttendanceRecord::query()
            ->with(['student', 'session.classSchedule.section', 'session.classSchedule.subject'])
            ->when($filters['school_year_id'] ?? null, fn ($query, $id) => $query->whereHas('session.classSchedule', fn ($query) => $query->where('school_year_id', $id)))
            ->when($filters['grade_level_id'] ?? null, fn ($query, $id) => $query->whereHas('session.classSchedule.section', fn ($query) => $query->where('grade_level_id', $id)))
            ->when($filters['section_id'] ?? null, fn ($query, $id) => $query->whereHas('session.classSchedule', fn ($query) => $query->where('section_id', $id)))
            ->when($filters['subject_id'] ?? null, fn ($query, $id) => $query->whereHas('session.classSchedule', fn ($query) => $query->where('subject_id', $id)))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereHas('session', fn ($query) => $query->whereDate('attendance_date', '>=', $date)))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereHas('session', fn ($query) => $query->whereDate('attendance_date', '<=', $date)))
            ->get()
            ->groupBy(fn (AttendanceRecord $record) => $record->student_id.'-'.$record->session->classSchedule->id);

        $rows = $records->map(function ($group): array {
            /** @var AttendanceRecord $first */
            $first = $group->first();

            return [
                $first->student->student_number,
                $first->student->fullName(),
                $first->session->classSchedule->section->code,
                $first->session->classSchedule->subject->name,
                $group->where('status', AttendanceRecord::STATUS_PRESENT)->count(),
                $group->where('status', AttendanceRecord::STATUS_ABSENT)->count(),
                $group->where('status', AttendanceRecord::STATUS_LATE)->count(),
                $group->where('status', AttendanceRecord::STATUS_EXCUSED)->count(),
            ];
        })->values()->all();

        return ['title' => 'Attendance Summary Report', 'headings' => ['Student No.', 'Name', 'Section', 'Subject', 'Present', 'Absent', 'Late', 'Excused'], 'rows' => $rows];
    }

    private function grades(array $filters): array
    {
        $rows = StudentGrade::query()
            ->with(['student', 'assessment.gradingPeriod', 'assessment.classSchedule.section', 'assessment.classSchedule.subject'])
            ->when($filters['school_year_id'] ?? null, fn ($query, $id) => $query->whereHas('assessment.classSchedule', fn ($query) => $query->where('school_year_id', $id)))
            ->when($filters['grade_level_id'] ?? null, fn ($query, $id) => $query->whereHas('assessment.classSchedule.section', fn ($query) => $query->where('grade_level_id', $id)))
            ->when($filters['grading_period_id'] ?? null, fn ($query, $id) => $query->whereHas('assessment', fn ($query) => $query->where('grading_period_id', $id)))
            ->when($filters['section_id'] ?? null, fn ($query, $id) => $query->whereHas('assessment.classSchedule', fn ($query) => $query->where('section_id', $id)))
            ->when($filters['subject_id'] ?? null, fn ($query, $id) => $query->whereHas('assessment.classSchedule', fn ($query) => $query->where('subject_id', $id)))
            ->get()
            ->map(fn (StudentGrade $grade): array => [
                $grade->student->student_number,
                $grade->student->fullName(),
                $grade->assessment->classSchedule->section->code,
                $grade->assessment->classSchedule->subject->name,
                $grade->assessment->gradingPeriod->name,
                $grade->assessment->title,
                $grade->score,
                $grade->assessment->max_score,
                str($grade->status)->headline()->toString(),
            ])
            ->all();

        return ['title' => 'Grade Report', 'headings' => ['Student No.', 'Name', 'Section', 'Subject', 'Period', 'Assessment', 'Score', 'Max Score', 'Status'], 'rows' => $rows];
    }

    private function teacherSchedules(array $filters): array
    {
        $rows = ClassSchedule::query()
            ->with(['teacher', 'subject', 'section', 'classroom'])
            ->when($filters['school_year_id'] ?? null, fn ($query, $id) => $query->where('school_year_id', $id))
            ->when($filters['grade_level_id'] ?? null, fn ($query, $id) => $query->whereHas('section', fn ($query) => $query->where('grade_level_id', $id)))
            ->when($filters['teacher_id'] ?? null, fn ($query, $id) => $query->where('teacher_id', $id))
            ->when($filters['section_id'] ?? null, fn ($query, $id) => $query->where('section_id', $id))
            ->when($filters['subject_id'] ?? null, fn ($query, $id) => $query->where('subject_id', $id))
            ->orderBy('day_of_week')
            ->orderBy('starts_at')
            ->get()
            ->map(fn (ClassSchedule $schedule): array => [
                $schedule->teacher->fullName(),
                $schedule->subject->name,
                $schedule->section->code,
                $schedule->classroom?->code ?? 'No room',
                str($schedule->day_of_week)->headline()->toString(),
                substr($schedule->starts_at, 0, 5),
                substr($schedule->ends_at, 0, 5),
                str($schedule->status)->headline()->toString(),
            ])
            ->all();

        return ['title' => 'Teacher Schedule Report', 'headings' => ['Teacher', 'Subject', 'Section', 'Room', 'Day', 'Start', 'End', 'Status'], 'rows' => $rows];
    }
}
