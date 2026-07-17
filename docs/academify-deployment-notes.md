# Academify Deployment Notes

These notes describe a conservative production deployment path for Academify. Always back up the database before running production migrations.

## Server Requirements

- PHP `^8.3`
- Composer 2
- Node.js `^20.19.0` or `>=22.12.0` and npm compatible with the Vite build pipeline
- MySQL
- Web server configured for Laravel public directory
- Writable `storage` and `bootstrap/cache` directories

Required PHP extensions used by the app and dependencies:

- `bcmath`
- `ctype`
- `curl`
- `dom`
- `fileinfo`
- `filter`
- `gd`
- `intl`
- `json`
- `mbstring`
- `mysqli`
- `openssl`
- `pdo_mysql`
- `tokenizer`
- `xml`
- `zip`

## Environment File

Create `.env` from `.env.example` and confirm:

```dotenv
APP_NAME=Academify
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.example

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=academify
DB_USERNAME=
DB_PASSWORD=

MAIL_FROM_ADDRESS=no-reply@your-domain.example
MAIL_FROM_NAME="${APP_NAME}"

QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
```

Do not commit production `.env` values.

## First Deployment

Install dependencies:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

If `npm run build` fails with a `node:util` `styleText` export error, upgrade Node.js to `20.19` or newer and run the build again.

Generate the app key if the environment does not already have one:

```bash
php artisan key:generate
```

Create the storage link:

```bash
php artisan storage:link
```

Run migrations:

```bash
php artisan migrate --force
```

Seed required roles, permissions, default users, and settings only when appropriate:

```bash
php artisan db:seed --force
```

For production, review seeded demo users immediately and replace local demo credentials with real administrator accounts.

## Cache Commands

After environment values are correct:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

When changing environment values, clear and rebuild caches:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Queue And Scheduler

Academify currently uses database queues in `.env.example`.

If queue processing is enabled in production, run a supervised worker:

```bash
php artisan queue:work --tries=3
```

If scheduled tasks are added later, configure the server cron to run:

```bash
php artisan schedule:run
```

## Verification After Deployment

Run:

```bash
php artisan migrate:status
php artisan route:list
php artisan test
```

Then manually verify:

- Administrator login
- Role-based menus
- User management
- Student record preview
- Teacher schedule
- Attendance entry
- Grade entry
- Announcements
- Report PDF and Excel exports
- Audit Logs
- System Settings

## Backup Guidance

Before each production release:

- Take a database backup.
- Confirm the backup can be restored.
- Keep a copy of the deployed commit hash.
- Keep a copy of the previous `.env` values outside the repository.

## Rollback Guidance

Code rollback:

- Re-deploy the previous known-good commit.
- Run `composer install --no-dev --optimize-autoloader`.
- Run `npm install` and `npm run build` if frontend assets changed.
- Rebuild Laravel caches.

Database rollback:

- Prefer forward-fix migrations whenever possible.
- Do not run destructive rollback commands against production without a reviewed backup and explicit approval.
- Restore from backup only when the team accepts the data-loss window.

## Production Safety Rules

- Never run database reset commands in production.
- Never truncate or drop production tables as part of routine deployment.
- Never commit secrets.
- Use migration files for schema changes.
- Keep seeders idempotent.
- Restrict report, audit, settings, and user management routes by permission.
