# Academify Attendance Management Plan

This plan defines the implementation stage after Scheduling Management. The goal is to let teachers record attendance for scheduled classes and let administrators review attendance by student, section, class schedule, and date range.

## Module Purpose

Attendance Management connects class schedules to daily attendance records. It should provide a simple, reliable workflow for teachers to mark attendance and for administrators to monitor attendance activity.

Primary users:

- Administrators
- Teachers with assigned class schedules
- Staff users with attendance management permissions

Students and guardians should not manage attendance in the initial version.

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

Attendance Management should follow the formal Academify admin layout used by the previous modules.

Design requirements:

- Use the authenticated Academify layout and sidebar navigation.
- Add Attendance as its own menu group for administrators.
- Add My Attendance Classes or Attendance Entry under the teacher menu.
- Use clean tables with filters for date, school year, section, teacher, schedule, and status.
- Keep teacher attendance entry fast and focused: class summary, attendance date, student list, status controls, notes, and save action.
- Use readable status badges for present, absent, late, and excused.
- Avoid complex dashboards in this phase; keep the interface formal, calm, and operational.

## Scope

Build:

- Attendance session list
- Create or open attendance session for a scheduled class and date
- Teacher attendance entry page
- Student attendance records per session
- Administrator attendance review page
- Student attendance history view
- Filters by date, school year, section, teacher, schedule, and attendance status

Do not build guardian/student attendance portals, SMS notifications, attendance analytics, PDF reports, bulk imports, biometric attendance, or payroll links in this phase.

## Data Model

Create migrations in small, non-destructive groups.

Core tables:

- `attendance_sessions`
- `attendance_records`

Recommended attendance session fields:

- class_schedule_id
- attendance_date
- status
- submitted_by
- submitted_at
- notes

Recommended attendance record fields:

- attendance_session_id
- student_id
- status
- notes

Session statuses:

- draft
- submitted

Record statuses:

- present
- absent
- late
- excused

Relationship rules:

- An attendance session belongs to one class schedule.
- An attendance session belongs to one attendance date.
- A class schedule can have only one attendance session per date.
- Attendance records belong to one attendance session.
- Attendance records reference enrolled students in the scheduled section.
- A student can have only one attendance record per session.
- Submitted sessions should remain editable only by authorized users.
- Attendance data should not be hard-deleted in normal workflows.

## Permissions

Seed permissions for:

- `attendance.view`
- `attendance.create`
- `attendance.update`
- `attendance.submit`
- `attendance.view_own`

Assign all attendance management permissions to the Administrator role.

Assign `attendance.view_own`, `attendance.create`, `attendance.update`, and `attendance.submit` to the Teacher role for assigned schedules only.

Navigation and routes should be hidden or blocked for users without the matching permission.

## Role Menu Matrix

Menus must be updated with every module. For this module:

- Administrator: Dashboard, Users, Academic Setup, Students, Guardians, Teachers, Enrollment, Scheduling, Attendance
- Teacher: Dashboard, My Teacher Profile, My Schedule, Attendance Entry
- Student: Dashboard only
- Parent/Guardian: Dashboard only

Attendance menu items must be permission-driven, so future staff roles can receive access without hard-coding role names in Blade views.

## Screens

Administrator screens:

- Attendance session list with date and schedule filters
- Attendance session detail page
- Student attendance history page

Teacher screens:

- My Attendance Classes page
- Attendance entry page for a selected schedule and date

Recommended route group:

- `/attendance/sessions`
- `/attendance/students/{student}`
- `/teacher/attendance`
- `/teacher/attendance/{classSchedule}`

## Validation Rules

Use Form Request classes or Livewire validation for every write.

Required validation:

- Class schedule is required and must exist.
- Attendance date is required and must be a valid date.
- A class schedule can only have one session per attendance date.
- Teachers can create or update attendance only for their own assigned schedules.
- Attendance record students must belong to the scheduled section through active enrollment.
- Attendance record status must be present, absent, late, or excused.
- Session status must be draft or submitted.
- Notes must remain optional and length-limited.

## Implementation Order

1. Add attendance permissions to the seeder.
2. Create migrations and models for attendance sessions and attendance records.
3. Add relationships to ClassSchedule, Student, Teacher, and User models.
4. Add factories and focused local seed data.
5. Add protected routes and role-aware menu entries.
6. Build administrator attendance session list and detail page.
7. Build teacher attendance schedule list.
8. Build teacher attendance entry workflow for scheduled section students.
9. Build student attendance history view.
10. Add validation for duplicate sessions, assigned teacher access, enrolled students, and record statuses.
11. Add feature tests for permissions, menus, validation, entry workflow, submission, and history.
12. Run migrations and seeders after tests pass.

## Safety Rules

- Do not reset, truncate, drop, recreate, or wipe any database.
- Do not use destructive database commands to make setup easier.
- Use additive migrations only.
- Keep seeders idempotent.
- Do not delete students, enrollments, class schedules, or teacher records when attendance changes.
- Use session status and record updates instead of hard delete for normal workflows.

## Testing Checklist

Add tests for:

- Administrators can access Attendance pages.
- Teachers can access attendance entry for their own schedules.
- Teachers cannot access attendance entry for another teacher's schedules.
- Students and guardians cannot access Attendance pages.
- Attendance menus appear only for authorized users.
- A teacher can create a draft attendance session.
- A teacher can mark students present, absent, late, or excused.
- A teacher can submit an attendance session.
- Duplicate sessions for the same schedule and date are rejected.
- Attendance records reject students not enrolled in the scheduled section.
- Administrators can view attendance session details.
- Student attendance history displays recorded attendance.
- Migrations and seeders run without destructive database actions.

## Acceptance Criteria

This phase is complete when:

- Teachers can record attendance for assigned scheduled classes.
- Attendance sessions are unique per class schedule and date.
- Attendance records are created for enrolled students in the scheduled section.
- Administrators can review attendance sessions and student attendance history.
- Attendance menus appear only for authorized users.
- All writes are validated.
- Attendance data can be updated without deleting related records.
- Tests cover permissions, menus, validation, teacher entry, submission, and history.
- Migrations and seeders have been run successfully.

## Later Phases

Attendance Management should only record and review class attendance. Attendance analytics, guardian/student attendance portals, reports, notifications, exports, disciplinary workflows, and billing links belong to later modules.
