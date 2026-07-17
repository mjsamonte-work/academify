# Academify Reports Management Plan

This plan defines the implementation stage after Announcements and Notifications. The goal is to let administrators generate filtered school reports from existing Academify records and export them in printable or tabular formats.

## Module Purpose

Reports Management provides read-only operational reporting for the core school workflows already built in Academify. It should help administrators review student, enrollment, attendance, grade, and teacher schedule data without changing source records.

Primary users:

- Administrators
- Staff users with report permissions

Teachers, students, and guardians should not access administrator reports in the initial version.

## Current Stack

- Laravel 13
- Livewire 4
- Flux UI
- Tailwind CSS 4
- Alpine.js
- Fortify authentication
- Spatie Permission
- MySQL target database

Required packages:

- `maatwebsite/excel` for Excel exports
- `barryvdh/laravel-dompdf` for PDF exports

## Design Direction

Reports Management should follow the formal Academify admin layout used by the previous modules.

Design requirements:

- Use the authenticated Academify layout and sidebar navigation.
- Add Reports as its own administrator menu group.
- Use clean report filter forms with school year, section, grade level, date range, teacher, subject, and status filters as applicable.
- Use readable tables optimized for scanning and export.
- Keep pages dense and operational, not dashboard-heavy.
- Use clear PDF and Excel export buttons.
- Avoid charts, analytics panels, and decorative summaries in this phase.

## Scope

Build:

- Student master list report
- Enrollment report
- Attendance summary report
- Grade report
- Teacher schedule report
- PDF export for printable reports
- Excel export for tabular reports

Do not build advanced analytics, dashboards, charts, custom report builders, scheduled emails, audit reports, financial reports, or parent/student report portals in this phase.

## Data Sources

Reports should read from existing tables only.

Primary source tables:

- `students`
- `guardians`
- `enrollments`
- `school_years`
- `grade_levels`
- `sections`
- `teachers`
- `subjects`
- `class_schedules`
- `attendance_sessions`
- `attendance_records`
- `grading_periods`
- `assessments`
- `student_grades`

No new core reporting data table is required for this phase.

## Report Definitions

Student master list:

- Student number
- Student name
- Grade level
- Section
- Status
- Guardian count
- Contact details

Enrollment report:

- Student number
- Student name
- School year
- Grade level
- Section
- Enrollment status
- Enrollment dates

Attendance summary report:

- Student number
- Student name
- Section
- Class schedule or subject
- Present count
- Absent count
- Late count
- Excused count
- Date range

Grade report:

- Student number
- Student name
- Section
- Subject
- Grading period
- Assessment
- Score
- Max score
- Grade status

Teacher schedule report:

- Teacher
- Subject
- Section
- Classroom
- Day
- Start time
- End time
- Status

## Permissions

Seed permissions for:

- `reports.view`
- `reports.export_pdf`
- `reports.export_excel`

Assign all report permissions to the Administrator role.

Navigation and routes should be hidden or blocked for users without the matching permission.

## Role Menu Matrix

Menus must be updated with every module. For this module:

- Administrator: Dashboard, Users, Academic Setup, Students, Guardians, Teachers, Enrollment, Scheduling, Attendance, Grades, Announcements, Reports
- Teacher: Dashboard, Notices, My Teacher Profile, My Schedule, Attendance Entry, Grade Entry
- Student: Dashboard, Notices
- Parent/Guardian: Dashboard, Notices

Reports menu items must be permission-driven, so future registrar or staff roles can receive access without hard-coding role names in Blade views.

## Screens

Administrator screens:

- Reports index
- Student master list report
- Enrollment report
- Attendance summary report
- Grade report
- Teacher schedule report

Recommended route group:

- `/reports`
- `/reports/students`
- `/reports/enrollments`
- `/reports/attendance`
- `/reports/grades`
- `/reports/teacher-schedules`

Each report should support:

- On-page filtered preview
- PDF export
- Excel export

## Validation Rules

Use Form Request classes or validated query objects for export and filter inputs.

Required validation:

- School year filters must reference existing school years.
- Grade level filters must reference existing grade levels.
- Section filters must reference existing sections.
- Teacher filters must reference existing teachers.
- Subject filters must reference existing subjects.
- Grading period filters must reference existing grading periods.
- Date range filters must use valid dates.
- End date must be after or equal to start date when both are provided.
- Export format must be PDF or Excel.
- Only authorized users can export reports.

## Implementation Order

1. Install report export packages if not already installed.
2. Add report permissions to the seeder.
3. Add protected report routes and role-aware menu entries.
4. Create shared report filter helpers or query classes.
5. Build Reports index page.
6. Build Student master list report preview and exports.
7. Build Enrollment report preview and exports.
8. Build Attendance summary report preview and exports.
9. Build Grade report preview and exports.
10. Build Teacher schedule report preview and exports.
11. Add feature tests for permissions, filters, previews, PDF exports, and Excel exports.
12. Run migrations and seeders after tests pass if permissions changed.

## Safety Rules

- Do not reset, truncate, drop, recreate, or wipe any database.
- Do not use destructive database commands to make setup easier.
- Reports must be read-only against source records.
- Do not create duplicate source records during report generation.
- Keep export generation temporary and non-destructive.
- Report permissions and seeders must be idempotent.

## Testing Checklist

Add tests for:

- Administrators can access Reports pages.
- Teachers, students, and guardians cannot access Reports pages.
- Reports menu appears only for authorized users.
- Student master list filters by grade level, section, and status.
- Enrollment report filters by school year, section, and status.
- Attendance summary filters by date range, section, and subject.
- Grade report filters by grading period, section, and subject.
- Teacher schedule report filters by teacher, section, and subject.
- PDF exports return a downloadable PDF response.
- Excel exports return a downloadable spreadsheet response.
- Invalid filters are rejected.
- Report generation does not modify source data.

## Acceptance Criteria

This phase is complete when:

- Administrators can preview core reports from existing Academify records.
- Administrators can export supported reports to PDF and Excel.
- Reports are protected by permissions and visible only to authorized users.
- Filters are validated and reliable.
- Report generation is read-only and non-destructive.
- Tests cover permissions, filters, previews, exports, and source-data safety.
- Seeders have been run successfully for report permissions.

## Later Phases

Reports Management should only provide basic operational reports. Advanced analytics, charts, scheduled report emails, custom report builders, financial reports, transcript/report-card generation, and audit reports belong to later modules.
