<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Grades\SaveStudentGradesRequest;
use App\Models\Assessment;
use App\Models\ClassSchedule;
use App\Models\Enrollment;
use App\Models\GradingPeriod;
use App\Models\StudentGrade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function index(): View
    {
        $teacher = request()->user()?->teacher()->first();

        return view('teacher.grades.index', [
            'teacher' => $teacher,
            'classSchedules' => $teacher
                ? ClassSchedule::query()
                    ->with(['schoolYear', 'section.gradeLevel', 'subject'])
                    ->where('teacher_id', $teacher->id)
                    ->where('status', ClassSchedule::STATUS_ACTIVE)
                    ->orderBy('day_of_week')
                    ->orderBy('starts_at')
                    ->get()
                : collect(),
        ]);
    }

    public function show(Request $request, ClassSchedule $classSchedule): View
    {
        $this->authorizeSchedule($classSchedule);

        $classSchedule->load(['schoolYear', 'section.gradeLevel', 'subject', 'teacher']);
        $gradingPeriods = GradingPeriod::query()
            ->where('school_year_id', $classSchedule->school_year_id)
            ->orderBy('sort_order')
            ->get();
        $gradingPeriodId = $request->integer('grading_period_id') ?: $gradingPeriods->first()?->id;
        $assessments = Assessment::query()
            ->with('studentGrades')
            ->where('class_schedule_id', $classSchedule->id)
            ->when($gradingPeriodId, fn ($query) => $query->where('grading_period_id', $gradingPeriodId))
            ->orderBy('due_date')
            ->get();

        return view('teacher.grades.show', [
            'classSchedule' => $classSchedule,
            'gradingPeriods' => $gradingPeriods,
            'gradingPeriodId' => $gradingPeriodId,
            'assessments' => $assessments,
        ]);
    }

    public function edit(Assessment $assessment): View
    {
        $assessment->load(['classSchedule.schoolYear', 'classSchedule.section.gradeLevel', 'classSchedule.subject', 'gradingPeriod', 'studentGrades']);
        $this->authorizeSchedule($assessment->classSchedule);

        $students = $this->studentsForAssessment($assessment);

        return view('teacher.grades.edit', [
            'assessment' => $assessment,
            'students' => $students,
            'gradesByStudent' => $assessment->studentGrades->keyBy('student_id'),
            'statuses' => [StudentGrade::STATUS_DRAFT, StudentGrade::STATUS_SUBMITTED],
        ]);
    }

    public function save(SaveStudentGradesRequest $request, Assessment $assessment): RedirectResponse
    {
        $validated = $request->validated();
        $status = $validated['grade_status'];

        foreach ($validated['grades'] as $grade) {
            $assessment->studentGrades()->updateOrCreate(
                ['student_id' => $grade['student_id']],
                [
                    'score' => $grade['score'] ?? null,
                    'remarks' => $grade['remarks'] ?? null,
                    'status' => $status,
                    'submitted_by' => $status === StudentGrade::STATUS_SUBMITTED ? $request->user()?->id : null,
                    'submitted_at' => $status === StudentGrade::STATUS_SUBMITTED ? now() : null,
                ],
            );
        }

        return redirect()->route('teacher.grades.assessments.edit', $assessment)
            ->with('status', $status === StudentGrade::STATUS_SUBMITTED ? 'Grades submitted.' : 'Grade draft saved.');
    }

    private function authorizeSchedule(ClassSchedule $classSchedule): void
    {
        abort_unless(request()->user()?->teacher?->id === $classSchedule->teacher_id, 403);
    }

    private function studentsForAssessment(Assessment $assessment)
    {
        return Enrollment::query()
            ->with('student')
            ->where('school_year_id', $assessment->classSchedule->school_year_id)
            ->where('section_id', $assessment->classSchedule->section_id)
            ->where('status', Enrollment::STATUS_ENROLLED)
            ->orderBy('student_id')
            ->get()
            ->pluck('student');
    }
}
