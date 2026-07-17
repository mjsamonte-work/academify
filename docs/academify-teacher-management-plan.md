# Academify Teacher Management Plan

This plan defines the implementation stage after Student and Guardian Management. The goal is to store teacher profiles, connect teachers to login accounts, assign teaching subjects, and prepare clean teacher records for enrollment, scheduling, attendance, and grades.

## Module Purpose

Teacher Management is the staff profile layer for Academify. It should let administrators maintain teacher records and prepare each teacher for future class and subject assignments.

Primary users:

- Administrators
- Staff users with teacher management permissions

Teachers should be able to view their own teacher area after login, but they should not manage teacher records in the initial version.

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

Teacher Management should follow the same formal Academify admin layout used by User Management, Academic Setup, and Student and Guardian Management.

Design requirements:

- Use the authenticated Academify layout and sidebar navigation.
- Add Teachers under the existing people or records menu area.
- Use a clean teacher table with search, status badges, subject summary, account status, and direct actions.
- Keep teacher profile pages structured and calm: identity summary, contact details, linked account, assigned subjects, and future schedule placeholder.
- Use simple form sections for identity, employment details, contact information, account linkage, and subject assignments.
- Avoid hard delete in normal workflows; use active/inactive status.

## Scope

Build:

- Teacher CRUD
- Teacher profile page
- Teacher status and contact details
- Link teacher records to user accounts
- Assign teachers to subjects from Academic Setup
- Teacher dashboard or teacher-specific landing page
- Search and filters by name, employee number, subject, account linkage, and status

Do not build schedules, attendance entry, grade entry, payroll, HR workflows, or teacher self-service profile editing in this phase.

## Data Model

Create migrations in small, non-destructive groups.

Core tables:

- `teachers`
- `teacher_subject`

Recommended teacher fields:

- employee_number
- user_id
- first_name
- middle_name
- last_name
- preferred_name
- email
- phone
- address
- job_title
- department
- employment_type
- hired_at
- status

Recommended pivot fields:

- teacher_id
- subject_id

Relationship rules:

- A teacher may be linked to one user account.
- A user account may be linked to one teacher profile.
- A teacher may teach many subjects.
- A subject may be assigned to many teachers.
- Employee numbers must be unique.
- Teacher email should be unique when provided.
- Teacher subjects must reference Academic Setup subjects.
- Records should use active/inactive status instead of hard delete for normal workflows.

## Permissions

Seed permissions for:

- `teachers.view`
- `teachers.create`
- `teachers.update`
- `teachers.deactivate`
- `teachers.assign_subjects`
- `teachers.view_own`

Assign all teacher management permissions to the Administrator role.

Assign `teachers.view_own` to the Teacher role.

Navigation and routes should be hidden or blocked for users without the matching permission.

## Role Menu Matrix

Menus must be updated with every module. For this module:

- Administrator: Dashboard, Users, Academic Setup, Students, Guardians, Teachers
- Teacher: Dashboard, My Teacher Profile
- Student: Dashboard only
- Parent/Guardian: Dashboard only

Teacher menu items must be permission-driven, so future staff roles can receive access without hard-coding role names in Blade views.

## Screens

Administrator screens:

- Teacher list with search, subject filter, account-link filter, and status filter
- Create teacher form
- Edit teacher form
- Teacher profile page with linked account and assigned subjects

Teacher screens:

- My Teacher Profile page
- Future schedule placeholder area for later Scheduling module

Recommended route group:

- `/records/teachers`
- `/teacher/profile`

## Validation Rules

Use Form Request classes or Livewire validation for every write.

Required validation:

- Employee number is required and unique.
- Teacher first name and last name are required.
- Email must be valid and unique when provided.
- Linked user account must exist when provided.
- Linked user account must have the Teacher role or be assignable to the Teacher role during save.
- A user account cannot be linked to more than one teacher profile.
- Subject IDs must exist in the subjects table.
- Status must be active or inactive.
- Employment type must use approved values.
- Hired date must be a valid date when provided.

## Implementation Order

1. Add teacher permissions to the seeder.
2. Create migrations and models for teachers and teacher-subject assignments.
3. Add factories and focused local seed data.
4. Add protected routes and menu entries for administrators and teachers.
5. Build administrator Teacher CRUD.
6. Build teacher profile page with account and subject details.
7. Build teacher-only profile view.
8. Add feature tests for permissions, menus, validation, CRUD, account linkage, and subject assignments.
9. Run migrations and seeders after tests pass.

## Safety Rules

- Do not reset, truncate, drop, recreate, or wipe any database.
- Do not use destructive database commands to make setup easier.
- Use additive migrations only.
- Use active/inactive state for records that may be referenced later.
- Keep seeders idempotent.
- Do not delete teacher records when removing subject assignments.
- Do not delete user accounts when unlinking a teacher profile.

## Testing Checklist

Add tests for:

- Administrators can access Teacher Management pages.
- Teachers can access their own teacher profile page.
- Students and guardians cannot access Teacher Management pages.
- Menus appear only for authorized users.
- Administrators can create and update teachers.
- Duplicate employee numbers are rejected.
- Duplicate linked user accounts are rejected.
- Teacher user accounts can be linked safely.
- Teachers can be assigned to multiple subjects.
- Subject assignments can be updated without deleting subjects.
- Teacher records can be deactivated without deleting data.
- Migrations and seeders run without destructive database actions.

## Acceptance Criteria

This phase is complete when:

- Administrators can manage teacher records.
- Teachers can be linked to user accounts.
- Teachers can be assigned to Academic Setup subjects.
- Teacher menus appear only for authorized users.
- Teacher profile pages show identity, contact, account, status, and assigned subjects.
- Teachers can log in and see their own teacher profile area.
- All writes are validated.
- Records can be deactivated without deleting data.
- Tests cover permissions, menus, validation, CRUD, account linkage, and subject assignment workflows.
- Migrations and seeders have been run successfully.

## Later Phases

Teacher Management should only prepare teacher profiles and subject readiness. Scheduling, attendance, grade entry, advisory assignments, payroll, reports, and announcements belong to later modules.
