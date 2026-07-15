# Academify Core Build Plan

This plan keeps Academify focused on the smallest useful school management system first. We will build the modules in order so each step has a working result before moving to the next one.

## Current Stack

- Laravel 13
- Livewire 4
- Flux UI
- Tailwind CSS 4
- Alpine.js
- Fortify authentication
- MySQL target database

Recommended packages to add when needed:

- `spatie/laravel-permission` for roles and permissions
- `maatwebsite/excel` for Excel imports and exports
- `barryvdh/laravel-dompdf` for PDF reports

## Build Principles

- Keep the first version simple and usable.
- Build module by module, with tests for important workflows.
- Use Laravel migrations only; never reset or wipe real data.
- Use policies, gates, and Spatie Permission for access control.
- Use Form Request classes or Livewire validation for all writes.
- Use Eloquent relationships, scopes, casts, factories, and seeders.
- Use soft deletes for records that should be recoverable.
- Keep Livewire pages thin by moving business rules into actions or services.
- Add audit logging to important changes after the core workflows are stable.

## MVP Modules

### 1. Foundation

Goal: Prepare the app structure and shared conventions.

Build:

- App layout, sidebar navigation, and dashboard shell
- Base roles: Administrator, Teacher, Student, Parent or Guardian
- Shared lookup tables for active/inactive status
- Basic seeders for roles and first administrator account

Done when:

- An administrator can log in and see the dashboard.
- Navigation is visible based on role.
- The database has initial roles and permissions.

### 2. User And Access Management

Goal: Manage accounts and control who can access each module.

Build:

- User CRUD for administrators
- Role assignment
- Password reset or invitation flow
- Login logs
- Permission checks for each module route

Core data:

- `users`
- `roles`
- `permissions`
- `login_logs`

Done when:

- Administrators can create users and assign roles.
- Teachers, students, and guardians only see their allowed pages.

### 3. Academic Setup

Goal: Define the school structure used by enrollment, scheduling, attendance, and grades.

Build:

- School years
- Semesters or terms
- Grade levels
- Sections
- Subjects
- Classrooms

Core data:

- `school_years`
- `semesters`
- `grade_levels`
- `sections`
- `subjects`
- `classrooms`

Done when:

- Administrators can configure an active school year.
- Grade levels, sections, and subjects can be managed.

### 4. Student And Guardian Management

Goal: Store student profiles and connect them to guardians.

Build:

- Student CRUD
- Guardian CRUD
- Student to guardian relationship
- Student profile page
- Search and filters by name, student number, grade level, and status

Core data:

- `students`
- `guardians`
- `student_guardian`

Done when:

- Administrators can create a student and attach one or more guardians.
- A guardian can be linked to multiple students if needed.

### 5. Teacher Management

Goal: Store teacher profiles and prepare them for class assignments.

Build:

- Teacher CRUD
- Teacher profile page
- Teacher status and contact details
- Link teacher records to user accounts

Core data:

- `teachers`
- `teacher_subject`

Done when:

- Administrators can create teacher profiles.
- Teachers can log in and see teacher-specific pages.

### 6. Enrollment

Goal: Enroll students into a school year, grade level, and section.

Build:

- Enrollment form
- Enrollment status: pending, enrolled, withdrawn, completed
- Enrollment history per student
- Current enrollment lookup

Core data:

- `enrollments`

Done when:

- A student can be enrolled in one active school year.
- Administrators can view enrollment history and current section.

### 7. Scheduling

Goal: Assign subjects, teachers, classrooms, and meeting times to sections.

Build:

- Class schedule CRUD
- Conflict checks for teacher, classroom, section, day, and time
- Teacher schedule view
- Section schedule view

Core data:

- `class_schedules`

Done when:

- Administrators can create schedules without double-booking.
- Teachers can view their assigned schedule.

### 8. Attendance

Goal: Let teachers record attendance for scheduled classes.

Build:

- Attendance session per class/date
- Attendance statuses: present, absent, late, excused
- Teacher attendance entry page
- Student attendance history

Core data:

- `attendance_sessions`
- `attendance_records`

Done when:

- Teachers can mark attendance for their classes.
- Administrators can review attendance by student, section, and date range.

### 9. Grade Management

Goal: Record student assessments and compute grades.

Build:

- Grading periods
- Assessment setup per subject and section
- Student grade entry
- Basic computed final grade
- Student and guardian grade view

Core data:

- `grading_periods`
- `assessments`
- `student_grades`

Done when:

- Teachers can enter grades for assigned classes.
- Students and guardians can view published grades.

### 10. Announcements And Notifications

Goal: Communicate school updates to users.

Build:

- Announcement CRUD
- Audience targeting by role, section, or everyone
- In-app notifications for important events

Core data:

- `announcements`
- `notifications`

Done when:

- Administrators can publish announcements.
- Users see announcements intended for their role or section.

### 11. Reports

Goal: Export the most important school records.

Build:

- Student master list
- Enrollment report
- Attendance summary
- Grade report
- Teacher schedule report
- PDF export for printable reports
- Excel export for tabular reports

Done when:

- Administrators can generate PDF and Excel reports from filtered data.

### 12. Audit Logs And Settings

Goal: Add accountability and basic configuration.

Build:

- Activity logs for create, update, delete, and restore actions
- Settings page for school name, address, logo, active school year, and grading options

Core data:

- `activity_logs`
- `settings`

Done when:

- Administrators can see who changed important records.
- System settings can be updated without editing code.

## Suggested Implementation Order

1. Install required packages.
2. Create roles and permissions.
3. Build academic setup.
4. Build students and guardians.
5. Build teachers.
6. Build enrollment.
7. Build scheduling.
8. Build attendance.
9. Build grades.
10. Build announcements and notifications.
11. Build reports.
12. Add audit logs and settings.
13. Add final tests, polish, and deployment checklist.

## First Database Pass

Create migrations in small groups:

1. Roles and permission seeders
2. Academic setup tables
3. Student, guardian, and teacher profile tables
4. Enrollment and schedule tables
5. Attendance tables
6. Grade tables
7. Announcement, log, and setting tables

Avoid large migrations that do everything at once. Smaller migrations are easier to review, test, and fix without risking data.

## Testing Checklist

Add tests as each module is built:

- Feature tests for each main CRUD workflow
- Permission tests for role-protected pages
- Validation tests for required fields and duplicate records
- Conflict tests for class schedules
- Attendance and grade computation tests
- Export tests for report generation

## Initial MVP Boundary

Include in the first working version:

- Authentication
- Roles and permissions
- Dashboard
- Academic setup
- Students and guardians
- Teachers
- Enrollment
- Scheduling
- Attendance
- Grades
- Basic announcements
- Basic reports

Defer until later:

- Online payments
- Learning management features
- Advanced analytics
- SMS integration
- Mobile app
- Complex grading formulas
- Parent-teacher appointment booking
