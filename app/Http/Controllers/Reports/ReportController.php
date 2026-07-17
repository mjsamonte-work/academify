<?php

namespace App\Http\Controllers\Reports;

use App\Exports\ArrayReportExport;
use App\Http\Controllers\Controller;
use App\Models\GradeLevel;
use App\Models\GradingPeriod;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Reports\AcademifyReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    private const REPORTS = [
        'students' => 'Student Master List',
        'enrollments' => 'Enrollment Report',
        'attendance' => 'Attendance Summary',
        'grades' => 'Grade Report',
        'teacher-schedules' => 'Teacher Schedule Report',
    ];

    public function index()
    {
        return view('reports.index', ['reports' => self::REPORTS]);
    }

    public function show(Request $request, AcademifyReport $reports, string $report)
    {
        abort_unless(array_key_exists($report, self::REPORTS), 404);

        $filters = $this->validatedFilters($request);
        $data = $reports->build($report, $filters);

        return view('reports.show', [
            'reportKey' => $report,
            'reports' => self::REPORTS,
            'data' => $data,
            ...$this->lookups(),
        ]);
    }

    public function export(Request $request, AcademifyReport $reports, string $report, string $format): Response|BinaryFileResponse
    {
        abort_unless(array_key_exists($report, self::REPORTS), 404);
        abort_unless(in_array($format, ['pdf', 'excel'], true), 404);
        abort_unless($request->user()?->can($format === 'pdf' ? 'reports.export_pdf' : 'reports.export_excel'), 403);

        $data = $reports->build($report, $this->validatedFilters($request));
        $filename = str($report)->replace('-', '_')->append('_report');

        if ($format === 'pdf') {
            return Pdf::loadView('reports.export-pdf', ['data' => $data])
                ->download($filename.'.pdf');
        }

        return Excel::download(new ArrayReportExport($data['headings'], $data['rows']), $filename.'.xlsx');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'school_year_id' => ['nullable', Rule::exists('school_years', 'id')],
            'grade_level_id' => ['nullable', Rule::exists('grade_levels', 'id')],
            'section_id' => ['nullable', Rule::exists('sections', 'id')],
            'teacher_id' => ['nullable', Rule::exists('teachers', 'id')],
            'subject_id' => ['nullable', Rule::exists('subjects', 'id')],
            'grading_period_id' => ['nullable', Rule::exists('grading_periods', 'id')],
            'status' => ['nullable', 'string', 'max:40'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
    }

    private function lookups(): array
    {
        return [
            'schoolYears' => SchoolYear::query()->orderByDesc('starts_at')->get(),
            'gradeLevels' => GradeLevel::query()->orderBy('sort_order')->get(),
            'sections' => Section::query()->orderBy('name')->get(),
            'teachers' => Teacher::query()->orderBy('last_name')->orderBy('first_name')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
            'gradingPeriods' => GradingPeriod::query()->orderBy('sort_order')->get(),
        ];
    }
}
