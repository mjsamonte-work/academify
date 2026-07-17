# Academify Audit Logs And Settings Plan

This plan defines the implementation stage after Reports Management. The goal is to add accountability for important system changes and provide a simple administrator settings area for school-level configuration.

## Module Purpose

Audit Logs And Settings gives Academify administrators a controlled way to review important record changes and manage basic school configuration without editing code.

Primary users:

- Administrators
- Future staff roles with audit or settings permissions

Teachers, students, and parent or guardian users should not access audit logs or system settings in the initial version.

## Current Stack

- Laravel 13
- Livewire 4
- Flux UI
- Tailwind CSS 4
- Alpine.js
- Fortify authentication
- Spatie Permission
- MySQL target database

## Design Direction

The module should follow the formal Academify admin layout used by the existing modules.

Design requirements:

- Use the authenticated Academify layout and sidebar navigation.
- Add an Administration menu group when audit logs or settings are available.
- Use restrained, professional table layouts for audit logs.
- Use a clean settings form with grouped fields and clear save actions.
- Keep pages operational and compact.
- Use readable labels, neutral colors, simple cards, and consistent spacing.
- Avoid analytics charts, decorative timelines, or overly visual dashboards in this phase.

## Scope

Build:

- Audit log migration and model
- Automatic audit logging for important create, update, and status-change actions
- Audit log index page
- Audit log detail page
- Basic filters by module, action, user, date range, and affected record
- Settings migration and model
- Administrator settings page
- School identity settings
- Active academic configuration settings
- Role-aware sidebar menu updates

Do not build advanced audit diff viewers, restore workflows, approval workflows, external log shipping, branding asset uploads, tenant settings, billing settings, or notification preference settings in this phase.

## Data Model

Create `activity_logs`:

- `id`
- `user_id`, nullable foreign key to `users`
- `action`
- `module`
- `subject_type`, nullable
- `subject_id`, nullable
- `subject_label`, nullable
- `old_values`, nullable JSON
- `new_values`, nullable JSON
- `ip_address`, nullable
- `user_agent`, nullable
- `created_at`

Create `settings`:

- `id`
- `key`, unique
- `value`, nullable JSON
- `group`
- `label`
- `type`
- `updated_by`, nullable foreign key to `users`
- timestamps

Use non-destructive migrations only. Do not reset, truncate, or recreate existing tables.

## Audited Modules

Log important write actions for:

- Users
- Academic setup records
- Students
- Guardians
- Teachers
- Enrollments
- Class schedules
- Attendance sessions and records
- Grading periods, assessments, and student grades
- Announcements
- Settings updates

Initial actions:

- `created`
- `updated`
- `deactivated`
- `activated`
- `submitted`
- `published`
- `archived`
- `withdrawn`
- `completed`

## Settings Fields

School identity:

- School name
- School code
- School email
- School phone
- School address

Academic defaults:

- Active school year
- Default grading period
- Default timezone display label

Report defaults:

- Report footer text
- Report prepared-by label

Settings should be editable only by authorized administrators.

## Permissions

Seed permissions for:

- `audit_logs.view`
- `settings.view`
- `settings.update`

Assign all three permissions to the Administrator role.

Navigation and routes should be permission-driven so future staff roles can receive access without hard-coded role checks.

## Role Menu Matrix

Menus must be updated with every module. For this module:

- Administrator: Dashboard, Users, Academic Setup, Students, Guardians, Teachers, Enrollment, Scheduling, Attendance, Grades, Announcements, Reports, Audit Logs, Settings
- Teacher: Dashboard, Notices, My Teacher Profile, My Schedule, Attendance Entry, Grade Entry
- Student: Dashboard, Notices
- Parent/Guardian: Dashboard, Notices

Audit Logs and Settings should appear under an Administration menu group for users with the matching permissions.

## Screens

Administrator screens:

- Audit Logs index
- Audit Log detail
- System Settings edit page

Recommended route group:

- `/administration/audit-logs`
- `/administration/audit-logs/{activityLog}`
- `/administration/settings`

## Validation Rules

Use Form Request classes or validated controller requests for all writes.

Settings validation:

- School name is required.
- School code is optional and limited to a short identifier.
- School email must be a valid email when present.
- School phone is optional and limited to a reasonable length.
- School address is optional text.
- Active school year must reference an existing school year when present.
- Default grading period must reference an existing grading period when present.
- Report footer and prepared-by labels are optional text fields.

Audit filter validation:

- User filters must reference existing users.
- Date filters must be valid dates.
- End date must be after or equal to start date.
- Module and action filters must be known values when supplied.

## Implementation Order

1. Add audit and settings permissions to the seeder.
2. Create non-destructive migrations for `activity_logs` and `settings`.
3. Create `ActivityLog` and `Setting` models with casts and relationships.
4. Add a small audit logging service or action class.
5. Add audit logging calls to important existing write workflows.
6. Create settings seed defaults.
7. Add protected administration routes.
8. Add Administration sidebar menu items.
9. Build Audit Logs index and detail pages.
10. Build System Settings edit page.
11. Add Form Request validation for settings updates and audit filters.
12. Add feature tests for permissions, audit creation, filtering, settings updates, menu visibility, and non-destructive migrations.
13. Run migrations and seeders after tests pass.

## Testing Checklist

- Administrator can view Audit Logs.
- Administrator can view and update System Settings.
- Teacher, student, and guardian users are blocked from Audit Logs and Settings.
- Administration menu items are permission-driven.
- Creating or updating important records creates an activity log entry.
- Settings updates create an activity log entry.
- Audit filters validate date ranges and known values.
- Settings validation rejects invalid email and invalid related IDs.
- Existing data remains intact after migrations and seeders.

## Acceptance Criteria

- The module introduces audit visibility without changing existing workflows for regular users.
- Administrators can review who changed important records and when.
- Administrators can update basic school settings through the app.
- Audit Logs and Settings are protected by permissions.
- Menus are updated for every role.
- Migrations and seeders are safe and non-destructive.
- Automated tests cover access, logging, settings updates, and menu visibility.
