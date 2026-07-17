# Academify Announcements And Notifications Plan

This plan defines the implementation stage after Grade Management. The goal is to let administrators publish school announcements and deliver simple in-app notifications to selected audiences.

## Module Purpose

Announcements and Notifications provide the first communication layer for Academify. It should let authorized staff create formal school updates and target them by role, section, or everyone without building a full messaging system yet.

Primary users:

- Administrators
- Staff users with announcement management permissions
- Teachers, students, and guardians as announcement recipients

Teachers, students, and guardians should only view announcements intended for them in the initial version.

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

Announcements and Notifications should follow the formal Academify admin layout used by the previous modules.

Design requirements:

- Use the authenticated Academify layout and sidebar navigation.
- Add Announcements as its own menu group for administrators.
- Add My Announcements or Notices under the common user area for recipients.
- Use clean tables with filters for audience, status, publish date, and priority.
- Keep announcement forms simple: title, body, priority, audience, publish window, and status.
- Use restrained badges for draft, scheduled, published, archived, normal, and urgent.
- Avoid chat, inbox, marketing-style cards, or social feed behavior in this phase.

## Scope

Build:

- Announcement CRUD for administrators
- Announcement detail page
- Audience targeting by everyone, role, or section
- Recipient announcement list
- In-app notification records for published announcements
- Mark notification as read
- Filters by status, priority, audience, and publish date

Do not build email delivery, SMS, push notifications, comments, replies, read receipts dashboards, file attachments, recurring announcements, or rich notification preferences in this phase.

## Data Model

Create migrations in small, non-destructive groups.

Core tables:

- `announcements`
- `announcement_audiences`
- `notifications`

Recommended announcement fields:

- title
- body
- priority
- status
- publish_at
- expires_at
- created_by

Recommended announcement audience fields:

- announcement_id
- audience_type
- role_name
- section_id

Recommended notification fields:

- user_id
- announcement_id
- read_at

Announcement statuses:

- draft
- scheduled
- published
- archived

Priorities:

- normal
- urgent

Audience types:

- everyone
- role
- section

Relationship rules:

- An announcement belongs to the user who created it.
- An announcement may have one or more audience rules.
- A role audience targets users with the selected Spatie role.
- A section audience targets enrolled students in the selected section and their linked guardians when available.
- A published announcement should create idempotent notification records for resolved recipients.
- A user can have only one notification per announcement.
- Announcement records should not be hard-deleted in normal workflows.

## Permissions

Seed permissions for:

- `announcements.view`
- `announcements.create`
- `announcements.update`
- `announcements.publish`
- `announcements.archive`
- `announcements.view_own`

Assign all announcement management permissions to the Administrator role.

Assign `announcements.view_own` to Teacher, Student, and Parent/Guardian roles.

Navigation and routes should be hidden or blocked for users without the matching permission.

## Role Menu Matrix

Menus must be updated with every module. For this module:

- Administrator: Dashboard, Users, Academic Setup, Students, Guardians, Teachers, Enrollment, Scheduling, Attendance, Grades, Announcements
- Teacher: Dashboard, My Teacher Profile, My Schedule, Attendance Entry, Grade Entry, Notices
- Student: Dashboard, Notices
- Parent/Guardian: Dashboard, Notices

Announcement menu items must be permission-driven, so future staff roles can receive access without hard-coding role names in Blade views.

## Screens

Administrator screens:

- Announcement list with filters
- Create announcement form
- Edit announcement form
- Announcement detail page
- Notification recipient preview or count

Recipient screens:

- My Notices list
- Notice detail page
- Mark notice as read action

Recommended route group:

- `/announcements`
- `/announcements/{announcement}`
- `/notices`
- `/notices/{notification}`

## Validation Rules

Use Form Request classes or Livewire validation for every write.

Required validation:

- Title is required and length-limited.
- Body is required and length-limited.
- Priority must be normal or urgent.
- Status must be draft, scheduled, published, or archived.
- Publish date must be a valid date when provided.
- Expiry date must be after publish date when provided.
- At least one audience rule is required before publishing.
- Role audience must use an existing role.
- Section audience must use an existing section.
- Only authorized users can publish or archive announcements.
- Notification read actions must belong to the authenticated user.

## Implementation Order

1. Add announcement permissions to the seeder.
2. Create migrations and models for announcements, announcement audiences, and notifications.
3. Add relationships to User, Section, Announcement, and Notification models.
4. Add factories and focused local seed data.
5. Add protected routes and role-aware menu entries.
6. Build administrator announcement list, create, edit, and detail screens.
7. Build audience target form handling.
8. Add recipient resolution service or action for published announcements.
9. Build recipient notice list and detail screens.
10. Add mark-as-read workflow.
11. Add feature tests for permissions, menus, validation, publishing, recipient resolution, and read state.
12. Run migrations and seeders after tests pass.

## Safety Rules

- Do not reset, truncate, drop, recreate, or wipe any database.
- Do not use destructive database commands to make setup easier.
- Use additive migrations only.
- Keep seeders idempotent.
- Do not delete users, roles, sections, enrollments, students, or guardians when announcements change.
- Use statuses instead of hard delete for normal announcement workflows.
- Notification creation must be idempotent.

## Testing Checklist

Add tests for:

- Administrators can access Announcement Management pages.
- Teachers, students, and guardians can access their own Notices page.
- Teachers, students, and guardians cannot access admin announcement management pages.
- Announcement menus appear only for authorized users.
- Administrators can create and update announcements.
- Publishing requires at least one valid audience rule.
- Role audience creates notifications for matching users.
- Section audience creates notifications for enrolled students and linked guardians when available.
- Notification creation does not duplicate records.
- Users can only view their own notifications.
- Users can mark their own notifications as read.
- Archived announcements no longer appear as active notices.
- Migrations and seeders run without destructive database actions.

## Acceptance Criteria

This phase is complete when:

- Administrators can manage announcements.
- Administrators can target announcements by everyone, role, or section.
- Published announcements create in-app notifications for intended recipients.
- Recipients can view their notices and mark them as read.
- Announcement menus appear only for authorized users.
- All writes are validated.
- Announcements can be archived without deleting data.
- Tests cover permissions, menus, validation, publishing, recipient targeting, notification uniqueness, and read state.
- Migrations and seeders have been run successfully.

## Later Phases

Announcements and Notifications should only provide basic in-app communication. Email, SMS, push notifications, attachments, comments, advanced read receipts, notification preferences, templates, and analytics belong to later modules.
