# Academify Student And Guardian Management Plan

This plan defines the implementation stage after Academic Setup. The goal is to store student profiles, store guardian profiles, connect students to one or more guardians, and prepare clean student records for enrollment, attendance, grades, and guardian access.

## Module Purpose

Student and Guardian Management is the core profile layer for Academify. It should let administrators maintain accurate student and guardian information without depending on enrollment workflows yet.

Primary users:

- Administrators
- Staff users with student management permissions

Students and guardians should not manage these records in the initial version.

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

Student and Guardian Management should follow the same formal admin layout as User Management and Academic Setup.

Design requirements:

- Use the authenticated Academify layout and sidebar navigation.
- Add a `Records` or `People` menu group visible only to authorized users.
- Use clean tables with search, filters, status badges, and clear actions.
- Keep profile pages information-dense but calm: summary header, key details, relationships, and recent setup status.
- Use simple form sections for identity, contact, academic placement, and guardian relationships.
- Avoid hard delete in normal workflows; use active/inactive status.

## Scope

Build:

- Student CRUD
- Guardian CRUD
- Student to guardian relationship management
- Student profile page
- Guardian profile page
- Search and filters by name, student number, grade level, section, and status

Do not build enrollment, attendance, grade viewing, payment, messaging, or guardian self-service in this phase.

## Data Model

Create migrations in small, non-destructive groups.

Core tables:

- `students`
- `guardians`
- `guardian_student`

Recommended student fields:

- student_number
- first_name
- middle_name
- last_name
- preferred_name
- birthdate
- gender
- email
- phone
- address
- grade_level_id
- section_id
- status

Recommended guardian fields:

- first_name
- middle_name
- last_name
- email
- phone
- alternate_phone
- address
- occupation
- status

Recommended pivot fields:

- student_id
- guardian_id
- relationship
- is_primary_contact
- can_pick_up
- receives_notifications

Relationship rules:

- A student may have many guardians.
- A guardian may be linked to many students.
- A student can have one primary contact.
- Student numbers must be unique.
- Guardian email should be unique when provided.
- Student grade level and section should reference Academic Setup records.
- Records should use active/inactive status instead of hard delete for normal workflows.

## Permissions

Seed permissions for:

- `students.view`
- `students.create`
- `students.update`
- `students.deactivate`
- `guardians.view`
- `guardians.create`
- `guardians.update`
- `guardians.deactivate`

Assign all student and guardian permissions to the Administrator role.

Navigation and routes should be hidden or blocked for users without the matching view permission.

## Role Menu Matrix

Menus must be updated with every module. For this module:

- Administrator: Dashboard, Users, Academic Setup, Students, Guardians
- Teacher: Dashboard only
- Student: Dashboard only
- Parent/Guardian: Dashboard only

Student and Guardian menu items must be permission-driven, so future staff roles can receive access without hard-coding role names in Blade views.

## Screens

Student screens:

- Student list with search, grade level filter, section filter, and status filter
- Create student form
- Edit student form
- Student profile page with linked guardians

Guardian screens:

- Guardian list with search and status filter
- Create guardian form
- Edit guardian form
- Guardian profile page with linked students

Relationship management:

- Attach an existing guardian to a student.
- Create a new guardian while editing or viewing a student if practical.
- Update relationship details: relationship, primary contact, pickup permission, notifications.
- Detach a guardian from a student without deleting either record.

Recommended route group:

- `/records/students`
- `/records/guardians`

## Validation Rules

Use Form Request classes or Livewire validation for every write.

Required validation:

- Student number is required and unique.
- Student first name and last name are required.
- Guardian first name and last name are required.
- Emails must be valid when provided.
- Student grade level and section must exist when provided.
- Section must belong to the selected grade level when both are provided.
- Status must be active or inactive.
- Relationship is required when linking a guardian to a student.
- Only one guardian can be marked as primary contact per student.

## Implementation Order

1. Add student and guardian permissions to the seeder.
2. Create migrations and models for students, guardians, and guardian-student relationships.
3. Add factories and focused local seed data.
4. Add protected routes and menu entries.
5. Build Student CRUD and student profile page.
6. Build Guardian CRUD and guardian profile page.
7. Build attach, update, and detach guardian relationship workflows.
8. Add feature tests for permissions, menus, validation, CRUD, and relationship workflows.
9. Run migrations and seeders after tests pass.

## Safety Rules

- Do not reset, truncate, drop, recreate, or wipe any database.
- Do not use destructive database commands to make setup easier.
- Use additive migrations only.
- Use active/inactive state for records that may be referenced later.
- Keep seeders idempotent.
- Do not delete student or guardian records when removing a relationship.

## Testing Checklist

Add tests for:

- Administrators can access Student and Guardian pages.
- Non-authorized roles cannot access Student and Guardian pages.
- Menus appear only for authorized users.
- Administrators can create and update students.
- Administrators can create and update guardians.
- Duplicate student numbers are rejected.
- Invalid grade level and section combinations are rejected.
- Students can be linked to multiple guardians.
- Guardians can be linked to multiple students.
- Only one primary guardian can exist per student.
- Detaching a guardian relationship does not delete the student or guardian.
- Migrations and seeders run without destructive database actions.

## Acceptance Criteria

This phase is complete when:

- Administrators can manage student records.
- Administrators can manage guardian records.
- Students and guardians can be linked with relationship details.
- Student and Guardian menus appear only for authorized users.
- Student profile pages show academic placement and linked guardians.
- Guardian profile pages show linked students.
- All writes are validated.
- Records can be deactivated without deleting data.
- Tests cover permissions, menus, validation, CRUD, and relationship workflows.
- Migrations and seeders have been run successfully.

## Later Phases

After Student and Guardian Management is complete, Academify can move into Teacher Management, Enrollment, Scheduling, Attendance, Grade Management, Announcements, Reports, Audit Logs, and Settings.
