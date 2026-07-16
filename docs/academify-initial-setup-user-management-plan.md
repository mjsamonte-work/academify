# Academify Initial Setup And User Management Plan

This plan defines the first implementation stage for Academify. The goal is to replace the default Laravel starter-kit presentation with Academify branding, establish the application foundation, and complete administrator-led user and access management before moving into academic modules.

## Project Identity

- App name: `Academify`
- Composer package identity: `academify/academify`
- Description: `A modern school management platform for academic operations.`
- Primary users: school administrators, teachers, students, and parents or guardians
- Target database: MySQL

Environment defaults to update:

- `APP_NAME=Academify`
- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=academify`
- `MAIL_FROM_ADDRESS=no-reply@academify.local`
- `MAIL_FROM_NAME="${APP_NAME}"`    
- `VITE_APP_NAME="${APP_NAME}"`

## Current Stack

- Laravel 13
- Livewire 4
- Flux UI
- Tailwind CSS 4
- Alpine.js
- Fortify authentication
- MySQL target database

Required package for this phase:

- `spatie/laravel-permission` for roles and permissions

Later packages such as Excel and PDF export tools should be deferred until reporting is planned.

## Design Direction

Academify should use a clean, formal, and modern administration layout. The first screens should feel stable and professional rather than decorative or marketing-focused.

Design requirements:

- Replace Laravel starter-kit naming, welcome content, placeholder panels, and default branding with Academify identity.
- Use a restrained neutral interface with clear spacing, readable typography, subtle borders, and simple cards only where they support scanning.
- Keep the dashboard practical: show key setup status, account counts, recent login activity, and next setup actions.
- Use a consistent sidebar layout with role-aware navigation.
- Prefer readable tables, compact filters, status badges, and clear form sections for user management.
- Keep page headings direct and formal, such as `Dashboard`, `Users`, `Roles`, and `Access Management`.
- Use Flux UI components consistently for buttons, forms, tables, dialogs, dropdowns, and alerts.

## Phase 1: Initial Setup

Goal: prepare Academify as a branded school management application with safe setup conventions and a usable administrator shell.

Build:

- Update project metadata from Laravel starter-kit defaults to Academify information.
- Update `.env.example` with Academify app name, MySQL defaults, and mail sender defaults.
- Replace default logo text, page titles, auth copy, welcome page content, dashboard placeholders, and sidebar branding.
- Create a formal dashboard shell for authenticated users.
- Create the main navigation structure for Dashboard and User Management.
- Add administrator seed data for the first working login.
- Use migrations and seeders only for database setup.

Safety rules:

- Do not reset, truncate, drop, recreate, or wipe any database.
- Do not use destructive database commands to make setup easier.
- Add new migrations for schema changes and new seeders for initial data.
- Keep seeders idempotent so they can run safely more than once.

Done when:

- The application presents itself as Academify, not Laravel Starter Kit.
- An administrator can log in and see the formal Academify dashboard.
- The sidebar and authenticated layout show Academify navigation.
- Initial setup can be completed with non-destructive migrations and seeders.

## Phase 2: User And Access Management

Goal: let administrators manage accounts and control access to protected areas.

Build:

- Install and configure `spatie/laravel-permission`.
- Seed base roles: Administrator, Teacher, Student, Parent/Guardian.
- Seed permissions for dashboard access and user management.
- Assign the first administrator account to the Administrator role.
- Build administrator-only user CRUD.
- Support role assignment during user create and edit flows.
- Add account status support, with at least active and inactive states.
- Add basic profile fields needed for account administration.
- Add permission checks to protected user management routes.
- Add login logs for audit visibility.
- Use Form Request classes or Livewire validation for every write action.

Core data:

- `users`
- `roles`
- `permissions`
- `model_has_roles`
- `model_has_permissions`
- `role_has_permissions`
- `login_logs`

User management screens:

- User list with search, role filter, status filter, and latest login summary.
- Create user form with name, email, password or invitation path, status, and role assignment.
- Edit user form with profile fields, status, and roles.
- User detail view with account information, assigned roles, and recent login logs.

Done when:

- Administrators can create, view, edit, deactivate, and assign roles to users.
- Non-administrators cannot access user management routes.
- Teachers, students, and parents or guardians only see navigation allowed by their role.
- Login activity is recorded and visible to administrators.

## Testing Checklist

Add tests for this phase before moving to academic modules:

- The app metadata, environment example, and UI labels no longer present the project as Laravel Starter Kit.
- Administrator login works with seeded administrator data.
- Roles and permissions are seeded correctly and idempotently.
- Administrator users can create, edit, deactivate, and assign roles to users.
- Unauthorized users are blocked from user management pages.
- Validation prevents missing required fields, duplicate email addresses, and invalid role assignments.
- Login logs are created on successful login.
- Migrations and seeders run without destructive database actions.

## Acceptance Criteria

This phase is complete when:

- Academify branding is visible across the public page, authentication views, dashboard, sidebar, and page titles.
- Default Laravel starter-kit placeholder details have been removed or replaced.
- The administrator dashboard is clean, formal, responsive, and usable.
- User and access management is protected by roles and permissions.
- All writes are validated.
- Tests cover the main authentication, permission, user management, and login-log workflows.
- The database setup remains non-destructive and safe for existing data.

## Later Phases

After this plan is complete, Academify can move into academic setup, student and guardian management, teacher management, enrollment, scheduling, attendance, grade management, announcements, reports, audit logs, and system settings. These modules are intentionally outside this initial plan.
