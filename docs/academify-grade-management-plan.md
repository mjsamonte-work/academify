# Academify Grade Management Plan

This plan defines the implementation stage after Attendance Management. The goal is to let administrators prepare grading periods and assessments, let teachers record grades for assigned scheduled classes, and preserve grade records for later report cards and analytics.

## Module Purpose

Grade Management connects class schedules, enrolled students, subjects, and teachers into structured assessment and grade records. It should provide a simple, controlled workflow for grade entry without building full report cards yet.

Primary users:

- Administrators
- Teachers with assigned class schedules
- Staff users with grade management permissions

Students and guardians should not manage grades. Student and guardian grade viewing can be prepared later after publishing rules are defined.

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

- No new package is required for this phase.

## Design Direction

Grade Management should follow the formal Academify admin layout used by the previous modules.

Design requirements:

- Use the authenticated Academify layout and sidebar navigation.
- Add Grades as its own menu group for administrators.
- Add Grade Entry under the teacher menu.
- Use clean tables with filters for school year, section, teacher, subject, grading period, and status.
- Keep grade entry focused: class summary, assessment list, enrolled student list, score inputs, remarks, and save action.
- Use simple status badges for draft, submitted, and published.
- Avoid report-card style layouts in this phase; keep the interface operational and admin-focused.

## Scope

Build:

- Grading period CRUD for administrators
- Assessment CRUD for administrators
- Teacher grade entry page for assigned class schedules
- Student grade records per assessment
- Grade summary per class schedule
- Administrator grade review page
- Filters by school year, section, teacher, subject, grading period, and status

Do not build report cards, grade publishing portals, GPA/transmutation rules, honors ranking, transcript exports, PDF generation, guardian notifications, or advanced analytics in this phase.

## Data Model

Create migrations in small, non-destructive groups.

Core tables:

- `grading_periods`
- `assessments`
- `student_grades`

Recommended grading period fields:

- school_year_id
- name
- starts_at
- ends_at
- sort_order
- status

Recommended assessment fields:

- class_schedule_id
- grading_period_id
- title
- assessment_type
- max_score
- weight
- due_date
- status
- notes

Recommended student grade fields:

- assessment_id
- student_id
- score
- remarks
- status
- submitted_by
- submitted_at

Grade statuses:

- draft
- submitted
- published

Relationship rules:

- A grading period belongs to one school year.
- An assessment belongs to one class schedule and one grading period.
- A student grade belongs to one assessment and one student.
- A student can have only one grade record per assessment.
- Grade records should reference enrolled students in the scheduled section.
- Teachers can enter grades only for their assigned schedules.
- Published grades should remain editable only by authorized users.
- Grade records should not be hard-deleted in normal workflows.

## Permissions

Seed permissions for:

- `grades.view`
- `grades.create`
- `grades.update`
- `grades.submit`
- `grades.publish`
- `grades.view_own`

Assign all grade management permissions to the Administrator role.

Assign `grades.view_own`, `grades.create`, `grades.update`, and `grades.submit` to the Teacher role for assigned schedules only.

Navigation and routes should be hidden or blocked for users without the matching permission.

## Role Menu Matrix

Menus must be updated with every module. For this module:

- Administrator: Dashboard, Users, Academic Setup, Students, Guardians, Teachers, Enrollment, Scheduling, Attendance, Grades
- Teacher: Dashboard, My Teacher Profile, My Schedule, Attendance Entry, Grade Entry
- Student: Dashboard only
- Parent/Guardian: Dashboard only

Grade menu items must be permission-driven, so future registrar or staff roles can receive access without hard-coding role names in Blade views.

## Screens

Administrator screens:

- Grading period list, create, edit, and detail
- Assessment list, create, edit, and detail
- Grade review page per assessment

Teacher screens:

- My Grade Classes page
- Grade entry page for a selected class schedule and grading period

Recommended route group:

- `/grades/grading-periods`
- `/grades/assessments`
- `/grades/assessments/{assessment}/records`
- `/teacher/grades`
- `/teacher/grades/{classSchedule}`

## Validation Rules

Use Form Request classes or Livewire validation for every write.

Required validation:

- Grading period school year is required and must exist.
- Grading period name is required.
- Grading period dates must be valid and stay inside the selected school year when provided.
- Assessment class schedule is required and must exist.
- Assessment grading period is required and must exist.
- Assessment grading period must belong to the same school year as the class schedule.
- Assessment title is required.
- Max score must be greater than zero.
- Weight must be zero or greater.
- Grade score must be numeric and cannot exceed the assessment max score.
- Grade record students must belong to the scheduled section through active enrollment.
- Teachers can create or update grades only for their own assigned schedules.
- Grade status must be draft, submitted, or published.
- Notes and remarks must remain optional and length-limited.

## Implementation Order

1. Add grade permissions to the seeder.
2. Create migrations and models for grading periods, assessments, and student grades.
3. Add relationships to SchoolYear, ClassSchedule, Student, Teacher, and User models.
4. Add factories and focused local seed data.
5. Add protected routes and role-aware menu entries.
6. Build administrator grading period screens.
7. Build administrator assessment screens.
8. Build teacher grade class list.
9. Build teacher grade entry workflow for enrolled students.
10. Build administrator assessment grade review page.
11. Add validation for assigned teacher access, enrolled students, score limits, and period/schedule school-year matching.
12. Add feature tests for permissions, menus, validation, grading period CRUD, assessment CRUD, grade entry, submission, and review.
13. Run migrations and seeders after tests pass.

## Safety Rules

- Do not reset, truncate, drop, recreate, or wipe any database.
- Do not use destructive database commands to make setup easier.
- Use additive migrations only.
- Keep seeders idempotent.
- Do not delete students, enrollments, schedules, teachers, subjects, or grading periods when grades change.
- Use statuses instead of hard delete for normal grading workflows.

## Testing Checklist

Add tests for:

- Administrators can access Grade Management pages.
- Teachers can access grade entry for their own schedules.
- Teachers cannot access grade entry for another teacher's schedules.
- Students and guardians cannot access Grade Management pages.
- Grade menus appear only for authorized users.
- Administrators can create and update grading periods.
- Administrators can create and update assessments.
- Invalid grading period dates are rejected.
- Assessment grading period and class schedule school year must match.
- Teachers can save draft student grades.
- Teachers can submit student grades.
- Scores above max score are rejected.
- Grade records reject students not enrolled in the scheduled section.
- Administrators can review assessment grade records.
- Migrations and seeders run without destructive database actions.

## Acceptance Criteria

This phase is complete when:

- Administrators can manage grading periods and assessments.
- Teachers can enter grades for assigned scheduled classes.
- Grade records are created only for enrolled students in the scheduled section.
- Score limits and school-year relationships are validated.
- Administrators can review grade records per assessment.
- Grade menus appear only for authorized users.
- All writes are validated.
- Grade data can be updated without deleting related records.
- Tests cover permissions, menus, validation, grading setup, teacher entry, submission, and admin review.
- Migrations and seeders have been run successfully.

## Later Phases

Grade Management should only record and review structured grade data. Report cards, grade publishing portals, student and guardian grade views, GPA rules, transcripts, exports, notifications, honors ranking, and analytics belong to later modules.
