# Academify Academic Setup Plan

This plan defines the next implementation stage after the app layout and user access management work. The goal is to let administrators configure the academic structure that later modules will depend on for student records, enrollment, scheduling, attendance, and grades.

## Module Purpose

Academic Setup is the administrative foundation for the school calendar and class structure. It should be simple, formal, and reliable before student, teacher, enrollment, and schedule workflows are added.

Primary users:

- Administrators
- Staff users with academic setup permissions

This module should be administrator-only for the initial version.

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

Academic Setup should follow the same formal admin style as the dashboard and user management pages.

Design requirements:

- Use the authenticated Academify layout and sidebar navigation.
- Add a top-level `Academic Setup` navigation group or item visible only to authorized users.
- Use compact tables, clear filters, direct labels, and restrained status badges.
- Keep forms simple and sectioned, with required fields clearly marked.
- Use confirmation states for destructive-looking actions, but prefer deactivate/archive behavior over deletion.
- Avoid decorative dashboards; prioritize quick scanning and accurate setup status.

## Scope

Build the following setup areas:

- School Years
- Terms or Semesters
- Grade Levels
- Sections
- Subjects
- Classrooms

Do not build enrollment, student profiles, teacher profiles, schedules, attendance, grades, reports, or announcements in this phase.

## Data Model

Create migrations in small, non-destructive groups.

Core tables:

- `school_years`
- `terms`
- `grade_levels`
- `sections`
- `subjects`
- `classrooms`

Recommended fields:

- `school_years`: name, starts_at, ends_at, is_active, status
- `terms`: school_year_id, name, starts_at, ends_at, sort_order, status
- `grade_levels`: name, code, sort_order, status
- `sections`: grade_level_id, name, code, capacity, status
- `subjects`: name, code, description, status
- `classrooms`: name, code, capacity, location, status

Relationship rules:

- A term belongs to a school year.
- A section belongs to a grade level.
- Only one school year can be active at a time.
- Codes should be unique per table where used.
- Records should use active/inactive status instead of hard delete for normal workflows.

## Permissions

Seed permissions for:

- `academic_setup.view`
- `academic_setup.create`
- `academic_setup.update`
- `academic_setup.deactivate`

Assign all academic setup permissions to the Administrator role.

Navigation and routes should be hidden or blocked for users without `academic_setup.view`.

## Role Menu Matrix

Menus must be updated with every module. For this module:

- Administrator: Dashboard, Users, School Years, Terms, Grade Levels, Sections, Subjects, Classrooms
- Teacher: Dashboard only
- Student: Dashboard only
- Parent/Guardian: Dashboard only

Academic Setup menu items must remain permission-driven through `academic_setup.view`, so future staff roles can be granted access without hard-coding role names in Blade views.

## Screens

Create administrator screens for each setup area:

- List page with search, status filter, and sort order where relevant.
- Create form.
- Edit form.
- Detail view only when useful; simple lookup tables may return to the list after save.

Recommended route group:

- `/academic/school-years`
- `/academic/terms`
- `/academic/grade-levels`
- `/academic/sections`
- `/academic/subjects`
- `/academic/classrooms`

## Validation Rules

Use Form Request classes or Livewire validation for every write.

Required validation:

- Names are required and limited to a reasonable length.
- Codes are unique where applicable.
- Dates must be valid.
- School year end date must be after start date.
- Term dates must stay within the selected school year.
- Section capacity and classroom capacity must be positive numbers when provided.
- Status must be one of active or inactive.

## Implementation Order

1. Add academic setup permissions to the seeder.
2. Create migrations and models for school years, terms, grade levels, sections, subjects, and classrooms.
3. Add factories and focused seed data for local development.
4. Add protected routes and navigation.
5. Build CRUD screens for school years and terms.
6. Build CRUD screens for grade levels and sections.
7. Build CRUD screens for subjects and classrooms.
8. Add feature tests for permissions, validation, and main CRUD workflows.

## Safety Rules

- Do not reset, truncate, drop, recreate, or wipe any database.
- Do not use destructive database commands to make setup easier.
- Use additive migrations only.
- Use active/inactive state for records that may be referenced later.
- Keep seeders idempotent.
- Do not assume demo data can replace real school data.

## Testing Checklist

Add tests for:

- Administrators can access academic setup pages.
- Non-administrators cannot access academic setup pages.
- Administrators can create and update each setup record.
- Required validation fails clearly.
- Duplicate codes are rejected.
- Only one active school year can exist.
- Term dates must fit inside the selected school year.
- Sections require a valid grade level.
- Migrations and seeders run without destructive database actions.

## Acceptance Criteria

This phase is complete when:

- Administrators can manage school years, terms, grade levels, sections, subjects, and classrooms.
- Academic setup navigation appears only for authorized users.
- Academic setup pages match the clean Academify admin layout.
- All writes are validated.
- Setup records can be deactivated without deleting data.
- The active school year is clearly visible.
- Tests cover permissions, validation, and core CRUD workflows.

## Later Phases

After Academic Setup is complete, Academify can move into Student and Guardian Management, Teacher Management, Enrollment, Scheduling, Attendance, Grade Management, Announcements, Reports, Audit Logs, and Settings.
