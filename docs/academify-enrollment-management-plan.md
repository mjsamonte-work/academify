# Academify Enrollment Management Plan

This plan defines the implementation stage after Teacher Management. The goal is to enroll students into a school year, grade level, and section while preserving enrollment history for future scheduling, attendance, grades, reports, and guardian access.

## Module Purpose

Enrollment Management connects student records to the active academic structure. It should let administrators maintain current enrollment status without changing student profile data directly.

Primary users:

- Administrators
- Staff users with enrollment management permissions

Students, guardians, and teachers should not manage enrollments in the initial version.

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

Enrollment Management should follow the formal Academify admin layout used by User Management, Academic Setup, Student and Guardian Management, and Teacher Management.

Design requirements:

- Use the authenticated Academify layout and sidebar navigation.
- Add Enrollment as its own menu group or under Records, visible only to authorized users.
- Use clean tables with search, filters, status badges, student placement, and clear actions.
- Keep enrollment profile/detail pages structured: student summary, school year, grade level, section, status, dates, and history.
- Use simple form sections for student selection, academic placement, enrollment dates, and status.
- Avoid destructive workflows; enrollment changes should update status or create history, not wipe records.

## Scope

Build:

- Enrollment list
- Create enrollment form
- Edit enrollment form
- Enrollment detail page
- Enrollment history per student
- Current enrollment lookup
- Status management: pending, enrolled, withdrawn, completed
- Filters by student, school year, grade level, section, and status

Do not build scheduling, attendance, grades, billing, admission applications, document uploads, or guardian self-service enrollment in this phase.

## Data Model

Create migrations in small, non-destructive groups.

Core table:

- `enrollments`

Recommended enrollment fields:

- student_id
- school_year_id
- grade_level_id
- section_id
- status
- enrolled_at
- withdrawn_at
- completed_at
- notes

Relationship rules:

- A student may have many enrollment records over time.
- A student may have only one active enrolled record per school year.
- Enrollment must reference an existing student.
- Enrollment must reference an existing school year.
- Grade level and section must reference Academic Setup records.
- Section must belong to the selected grade level.
- Current enrollment should be determined from the latest active enrolled record.
- Enrollment records should not be hard-deleted in normal workflows.

## Permissions

Seed permissions for:

- `enrollments.view`
- `enrollments.create`
- `enrollments.update`
- `enrollments.withdraw`
- `enrollments.complete`

Assign all enrollment permissions to the Administrator role.

Navigation and routes should be hidden or blocked for users without the matching permission.

## Role Menu Matrix

Menus must be updated with every module. For this module:

- Administrator: Dashboard, Users, Academic Setup, Students, Guardians, Teachers, Enrollment
- Teacher: Dashboard, My Teacher Profile
- Student: Dashboard only
- Parent/Guardian: Dashboard only

Enrollment menu items must be permission-driven, so future registrar or staff roles can receive access without hard-coding role names in Blade views.

## Screens

Enrollment screens:

- Enrollment list with search, school year filter, grade level filter, section filter, and status filter
- Create enrollment form
- Edit enrollment form
- Enrollment detail page
- Student enrollment history area

Recommended route group:

- `/enrollment/enrollments`

## Validation Rules

Use Form Request classes or Livewire validation for every write.

Required validation:

- Student is required and must exist.
- School year is required and must exist.
- Grade level is required and must exist.
- Section is required and must exist.
- Section must belong to the selected grade level.
- Status must be pending, enrolled, withdrawn, or completed.
- Enrolled date must be a valid date when provided.
- Withdrawn date is required when status is withdrawn.
- Completed date is required when status is completed.
- A student cannot have more than one enrolled record for the same school year.
- Notes must remain optional and length-limited.

## Implementation Order

1. Add enrollment permissions to the seeder.
2. Create migration and model for enrollments.
3. Add enrollment relationships to Student, SchoolYear, GradeLevel, and Section models.
4. Add factory and focused local seed data.
5. Add protected routes and role-aware menu entries.
6. Build Enrollment list, create, edit, and detail screens.
7. Add student enrollment history display.
8. Add validation for section placement and duplicate active school-year enrollment.
9. Add feature tests for permissions, menus, validation, CRUD, status changes, and history.
10. Run migrations and seeders after tests pass.

## Safety Rules

- Do not reset, truncate, drop, recreate, or wipe any database.
- Do not use destructive database commands to make setup easier.
- Use additive migrations only.
- Keep seeders idempotent.
- Do not delete student records when enrollment status changes.
- Do not delete enrollment records when a student withdraws or completes a school year.
- Use status transitions instead of hard delete for normal enrollment workflows.

## Testing Checklist

Add tests for:

- Administrators can access Enrollment pages.
- Teachers, students, and guardians cannot access Enrollment pages.
- Enrollment menu appears only for authorized users.
- Administrators can create an enrollment.
- Administrators can update enrollment placement and notes.
- Duplicate enrolled records for the same student and school year are rejected.
- Invalid grade level and section combinations are rejected.
- Withdrawn enrollments require a withdrawn date.
- Completed enrollments require a completed date.
- Student enrollment history displays previous records.
- Migrations and seeders run without destructive database actions.

## Acceptance Criteria

This phase is complete when:

- Administrators can enroll students into a school year, grade level, and section.
- Administrators can view and update enrollment records.
- Enrollment history is visible per student or enrollment detail.
- Duplicate active enrollment for the same student and school year is prevented.
- Enrollment menus appear only for authorized users.
- All writes are validated.
- Enrollment records can be withdrawn or completed without deleting data.
- Tests cover permissions, menus, validation, CRUD, status transitions, and history.
- Migrations and seeders have been run successfully.

## Later Phases

Enrollment Management should only connect students to academic placement and preserve history. Scheduling, attendance, grades, billing, admissions, reports, and announcements belong to later modules.
