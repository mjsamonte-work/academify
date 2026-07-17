# Academify Scheduling Management Plan

This plan defines the implementation stage after Enrollment Management. The goal is to assign subjects, teachers, classrooms, sections, days, and times into clean class schedules that can later support attendance, grades, reports, and teacher schedule views.

## Module Purpose

Scheduling Management connects Academic Setup, Teacher Management, and Enrollment placement into class meeting records. It should let administrators create schedules while preventing common conflicts.

Primary users:

- Administrators
- Staff users with scheduling management permissions
- Teachers with permission to view their own schedule

Students and guardians should not manage schedules in the initial version.

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

Scheduling Management should follow the formal Academify admin layout used by the previous modules.

Design requirements:

- Use the authenticated Academify layout and sidebar navigation.
- Add Scheduling as its own menu group, visible only to authorized users.
- Use clean tables with filters for school year, section, teacher, subject, classroom, day, and status.
- Keep schedule detail pages structured: section, subject, teacher, classroom, meeting day, time, status, and conflict context.
- Use simple form sections for academic placement, teaching assignment, classroom assignment, and meeting time.
- Provide a teacher-only schedule view that is calm and easy to scan.
- Avoid decorative or marketing-style UI; keep it formal, dense, readable, and admin-focused.

## Scope

Build:

- Class schedule list
- Create class schedule form
- Edit class schedule form
- Class schedule detail page
- Teacher schedule view
- Section schedule view
- Conflict checks for teacher, classroom, and section time overlaps
- Filters by school year, section, teacher, subject, classroom, day, and status

Do not build attendance sessions, grade entry, timetable printing, recurring calendar exports, substitutions, room reservations, or student-facing schedule pages in this phase.

## Data Model

Create migrations in small, non-destructive groups.

Core table:

- `class_schedules`

Recommended class schedule fields:

- school_year_id
- section_id
- subject_id
- teacher_id
- classroom_id
- day_of_week
- starts_at
- ends_at
- status
- notes

Relationship rules:

- A class schedule must belong to one school year.
- A class schedule must belong to one section.
- A class schedule must reference one subject.
- A class schedule must reference one teacher.
- A class schedule may reference one classroom.
- The teacher should be assigned to the selected subject when possible.
- Start time must be before end time.
- Teacher, classroom, and section cannot be double-booked for overlapping active schedules in the same school year and day.
- Records should use active/inactive status instead of hard delete for normal workflows.

## Permissions

Seed permissions for:

- `schedules.view`
- `schedules.create`
- `schedules.update`
- `schedules.deactivate`
- `schedules.view_own`

Assign all schedule management permissions to the Administrator role.

Assign `schedules.view_own` to the Teacher role.

Navigation and routes should be hidden or blocked for users without the matching permission.

## Role Menu Matrix

Menus must be updated with every module. For this module:

- Administrator: Dashboard, Users, Academic Setup, Students, Guardians, Teachers, Enrollment, Scheduling
- Teacher: Dashboard, My Teacher Profile, My Schedule
- Student: Dashboard only
- Parent/Guardian: Dashboard only

Scheduling menu items must be permission-driven, so future registrar or staff roles can receive access without hard-coding role names in Blade views.

## Screens

Administrator screens:

- Schedule list with search and filters
- Create schedule form
- Edit schedule form
- Schedule detail page
- Section schedule page

Teacher screens:

- My Schedule page

Recommended route group:

- `/scheduling/class-schedules`
- `/scheduling/sections/{section}`
- `/teacher/schedule`

## Validation Rules

Use Form Request classes or Livewire validation for every write.

Required validation:

- School year is required and must exist.
- Section is required and must exist.
- Subject is required and must exist.
- Teacher is required and must exist.
- Classroom must exist when provided.
- Day of week must use approved values.
- Start time is required.
- End time is required and must be after start time.
- Status must be active or inactive.
- Teacher cannot have overlapping active schedules in the same school year and day.
- Classroom cannot have overlapping active schedules in the same school year and day when classroom is provided.
- Section cannot have overlapping active schedules in the same school year and day.
- Notes must remain optional and length-limited.

## Implementation Order

1. Add scheduling permissions to the seeder.
2. Create migration and model for class schedules.
3. Add relationships to SchoolYear, Section, Subject, Teacher, and Classroom models.
4. Add factory and focused local seed data.
5. Add protected routes and role-aware menu entries.
6. Build schedule list, create, edit, and detail screens.
7. Build section schedule view.
8. Build teacher-only My Schedule view.
9. Add conflict validation for teacher, classroom, and section overlaps.
10. Add feature tests for permissions, menus, validation, CRUD, conflict checks, and teacher schedule visibility.
11. Run migrations and seeders after tests pass.

## Safety Rules

- Do not reset, truncate, drop, recreate, or wipe any database.
- Do not use destructive database commands to make setup easier.
- Use additive migrations only.
- Keep seeders idempotent.
- Do not delete teacher, subject, section, classroom, or enrollment records when schedules change.
- Use active/inactive state for schedules that should no longer be used.

## Testing Checklist

Add tests for:

- Administrators can access Scheduling pages.
- Teachers can access their own schedule page.
- Students and guardians cannot access Scheduling pages.
- Scheduling menu appears only for authorized users.
- Administrators can create and update class schedules.
- Start time must be before end time.
- Teacher overlapping schedules are rejected.
- Classroom overlapping schedules are rejected.
- Section overlapping schedules are rejected.
- Teacher schedule view shows only that teacher's schedules.
- Section schedule view shows schedules for the selected section.
- Migrations and seeders run without destructive database actions.

## Acceptance Criteria

This phase is complete when:

- Administrators can manage class schedules.
- Schedules connect school year, section, subject, teacher, classroom, day, and time.
- Conflict checks prevent double-booking for teachers, classrooms, and sections.
- Teachers can log in and view their own schedule.
- Scheduling menus appear only for authorized users.
- All writes are validated.
- Schedule records can be deactivated without deleting data.
- Tests cover permissions, menus, validation, CRUD, conflict checks, and teacher schedule visibility.
- Migrations and seeders have been run successfully.

## Later Phases

Scheduling Management should only create and protect class meeting schedules. Attendance sessions, grade entry, student schedule pages, guardian views, calendar exports, reports, and announcements belong to later modules.
