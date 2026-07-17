# Academify Release Readiness Checklist

Use this checklist before tagging or deploying the first Academify release. Keep every database step non-destructive and back up production data before running migrations.

## Environment

- Confirm `APP_NAME=Academify`.
- Confirm `APP_ENV=production` for production.
- Confirm `APP_DEBUG=false` for production.
- Confirm `APP_URL` uses the final HTTPS domain.
- Confirm MySQL database credentials point to the intended database.
- Confirm `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME` use Academify sender details.
- Confirm `QUEUE_CONNECTION` is configured for the deployment environment.
- Confirm Node.js is `20.19` or newer before building frontend assets.
- Confirm no real secrets are committed to tracked files.

## Local Demo Users

Seeded local users use password `password` unless changed manually.

- Administrator: `admin@academify.local`
- Teacher: `teacher@academify.local`
- Student: `student@academify.local`
- Parent/Guardian: `guardian@academify.local`

Do not use these credentials in production.

## Role Menu Verification

Administrator should see:

- Dashboard
- Users
- Academic Setup
- Students
- Guardians
- Teachers
- Enrollment
- Scheduling
- Attendance
- Grades
- Announcements
- Reports
- Audit Logs
- Settings

Teacher should see:

- Dashboard
- Notices
- My Teacher Profile
- My Schedule
- Attendance Entry
- Grade Entry

Student should see:

- Dashboard
- Notices

Parent/Guardian should see:

- Dashboard
- Notices

## Module Smoke Tests

Run these checks with a seeded local or staging database:

- Login as Administrator.
- Create or edit a non-critical student record.
- Confirm an audit log appears for the change.
- Open Academic Setup pages.
- Open Enrollment, Scheduling, Attendance, Grades, and Announcements pages.
- Open Reports and preview the Student Master List.
- Export one PDF report.
- Export one Excel report.
- Open System Settings and save a harmless label change.
- Login as Teacher and confirm teacher pages load.
- Login as Student and confirm only dashboard and notices are visible.
- Login as Parent/Guardian and confirm only dashboard and notices are visible.

## Automated Verification

Run:

```bash
php artisan test
vendor/bin/pint --test
php artisan route:list
npm run build
```

Expected result:

- Feature tests pass.
- Pint passes.
- Completed module routes are registered.
- Frontend assets build successfully on Node.js `20.19` or newer.

## Database Safety

- Back up production database before deployment.
- Run migrations incrementally.
- Use `php artisan migrate --force` in production.
- Run seeders only when permission or default setting changes are required.
- Never reset, drop, truncate, or recreate production data.
- Confirm seeders are idempotent before production use.

## Report Export Checks

- PDF export downloads successfully.
- Excel export downloads successfully.
- Exported records match the filtered preview.
- Unauthorized users cannot open report pages or exports.

## Audit And Settings Checks

- Creating or updating a core record creates an audit log entry.
- Audit Logs are visible to Administrator only.
- System Settings are visible to Administrator only.
- Settings updates create audit log entries.
- Invalid settings inputs show validation errors.

## Final Sign-Off

- Application identity uses Academify branding.
- All role menus match the role matrix.
- Protected routes block unauthorized users.
- No default Laravel starter placeholder content appears in user-facing pages.
- Deployment notes have been reviewed.
- Backup and rollback steps are understood before production release.
