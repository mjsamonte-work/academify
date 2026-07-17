# Academify Final QA And Deployment Readiness Plan

This plan defines the implementation stage after Audit Logs And Settings. The goal is to prepare Academify for a stable first release by tightening quality, consistency, environment readiness, and deployment documentation.

## Module Purpose

Final QA And Deployment Readiness is not a new school workflow module. It is the release-hardening stage for the completed Academify MVP.

Primary users:

- Project owner
- Administrators
- Developers and deployment operators

This phase should make the existing application easier to verify, deploy, and operate safely.

## Current Stack

- Laravel 13
- Livewire 4
- Flux UI
- Tailwind CSS 4
- Alpine.js
- Fortify authentication
- Spatie Permission
- MySQL target database
- Laravel Excel
- Laravel DomPDF

## Design Direction

The application design should remain formal, clean, and administration-focused.

Design review requirements:

- Keep all module pages aligned with the authenticated Academify layout.
- Ensure sidebar menus remain permission-driven for every role.
- Keep table, form, filter, button, and status-badge styling consistent.
- Ensure empty states are clear and professional.
- Ensure text fits on desktop and mobile layouts.
- Avoid marketing-style pages, oversized decorative sections, and visual clutter.

## Scope

Build and verify:

- Release readiness checklist document
- Environment example review
- Production deployment notes
- Role-based navigation QA
- Full feature test suite cleanup if needed
- Basic smoke-test checklist
- UI consistency pass across existing modules
- Non-destructive database migration checklist
- Seeded local demo credential documentation
- Backup and rollback guidance

Do not build new academic workflows, payment features, LMS features, analytics dashboards, mobile apps, SMS integrations, or complex grading formulas in this phase.

## Release Readiness Areas

Application identity:

- App name is Academify.
- Default Laravel placeholder wording is removed from user-facing pages.
- Dashboard, auth screens, sidebar, and module pages use Academify wording.

Access control:

- Administrator has full MVP access.
- Teacher only sees teacher workflows and notices.
- Student only sees dashboard and notices.
- Parent/Guardian only sees dashboard and notices.
- Unauthorized users are blocked from protected pages.

Data safety:

- Migrations are additive or incremental.
- Seeders are idempotent.
- No reset, truncate, drop, or destructive database commands are used.
- Deployment notes recommend database backup before production changes.

Quality:

- `php artisan test` passes.
- `vendor/bin/pint --test` passes.
- Important routes are registered.
- Existing feature tests cover the main workflows.
- Export features for reports still work.

## Documentation Deliverables

Create or update:

- `docs/academify-release-readiness-checklist.md`
- `docs/academify-deployment-notes.md`

Release readiness checklist should include:

- Environment setup
- Permissions and seeded users
- Role menu verification
- Module smoke tests
- Report export checks
- Audit log checks
- Settings checks
- Backup reminder
- Final test commands

Deployment notes should include:

- Required PHP extensions
- Composer install command
- Environment variables to confirm
- Key generation requirement
- Storage link requirement
- Migration and seeding approach
- Queue and scheduler notes if applicable
- Cache commands
- Safe rollback guidance

## Environment Review

Confirm `.env.example` includes clear placeholders for:

- `APP_NAME=Academify`
- App URL
- Database connection
- Mail sender name and address
- Queue connection
- Session settings

Do not add real secrets or local private credentials to tracked files.

## Role Menu Matrix

Menus must remain updated for every role:

- Administrator: Dashboard, Users, Academic Setup, Students, Guardians, Teachers, Enrollment, Scheduling, Attendance, Grades, Announcements, Reports, Audit Logs, Settings
- Teacher: Dashboard, Notices, My Teacher Profile, My Schedule, Attendance Entry, Grade Entry
- Student: Dashboard, Notices
- Parent/Guardian: Dashboard, Notices

This phase should verify the menu matrix rather than introduce new role menus.

## QA Smoke Tests

Manual smoke-test checklist:

- Login as Administrator.
- Confirm administrator sidebar menus.
- Create or edit a non-critical student record.
- Confirm audit log is created.
- Open System Settings.
- Open Reports and export one PDF and one Excel file.
- Login as Teacher.
- Confirm teacher sidebar menus only.
- Open teacher schedule, attendance entry, and grade entry.
- Login as Student.
- Confirm only dashboard and notices are visible.
- Login as Parent/Guardian.
- Confirm only dashboard and notices are visible.

## Implementation Order

1. Review current docs, routes, sidebar, and seeded roles.
2. Create release readiness checklist documentation.
3. Create deployment notes documentation.
4. Review `.env.example` for Academify defaults and safe placeholders.
5. Run route, migration, test, and formatting checks.
6. Add or adjust tests only if release-readiness gaps are found.
7. Perform UI consistency pass on major module index pages.
8. Confirm seeded local demo users still work.
9. Confirm report exports still pass tests.
10. Confirm audit logs and settings still pass tests.
11. Run safe migrations and seeders only if documentation or seed data changes require it.
12. Record final verification commands and outcomes.

## Testing Checklist

- Full feature test suite passes.
- Pint formatting passes.
- Route list includes all completed modules.
- Migrations run without destructive operations.
- Seeder remains idempotent.
- Role menus match the matrix.
- Unauthorized access tests remain green.
- Report export tests remain green.
- Audit and settings tests remain green.

## Acceptance Criteria

- Academify has a clear release readiness checklist.
- Academify has practical deployment notes.
- Environment defaults are Academify-specific and safe.
- No new product scope is introduced.
- Existing module tests pass.
- UI and navigation remain consistent and permission-driven.
- Database guidance remains non-destructive.
