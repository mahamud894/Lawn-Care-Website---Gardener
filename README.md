<h1 align="center">🌿 Gardener</h1>

<p align="center">
  <b>Lawn care & gardening business website — online booking, gardener portal, payments and admin panel.</b>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white" alt="Bootstrap 5">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/Vite-7-646CFF?logo=vite&logoColor=white" alt="Vite 7">
  <br>
  <img src="https://img.shields.io/badge/database-MySQL%20%7C%20SQLite-4479A1?logo=mysql&logoColor=white" alt="MySQL or SQLite">
  <img src="https://img.shields.io/badge/payments-bKash%20%7C%20Nagad%20%7C%20Cards-E2136E" alt="bKash, Nagad, cards via SSLCommerz">
  <img src="https://img.shields.io/badge/language-English%20%7C%20%E0%A6%AC%E0%A6%BE%E0%A6%82%E0%A6%B2%E0%A6%BE-2E7D32" alt="English and Bangla">
  <img src="https://img.shields.io/badge/tested%20with-Pest-F28D1A" alt="Tested with Pest">
</p>

---

A full lawn care business website built with **Laravel 12**. Customers can browse services, book a visit for a specific date and time slot, track their bookings and leave reviews. Gardeners see their jobs on their phone, and the owner runs everything from an admin panel.

## Features

**For customers**
- Service, About, Projects, Blog, FAQ and Contact pages — every service has its own page
- English / বাংলা language switcher
- Online booking with live time-slot availability; each slot takes one booking per active gardener
- Live price estimate from lawn size, optional lawn photos, and repeating bookings (weekly / every 2 weeks / monthly)
- Personal dashboard: track status and gardener, cancel, reschedule, stop repeats, pay online, print invoices, leave reviews
- Online payment with SSLCommerz (bKash, Nagad, Rocket, cards) or cash on the day
- E-mail and SMS updates; floating WhatsApp button
- Sign up, login, "Continue with Google", forgot password, profile page

**For gardeners**
- Their own login (created from the team form) showing today's and upcoming jobs with phone, map link and photos
- Mark jobs done from the phone and record cash collected

**For the admin**
- Dashboard with live counts (bookings by status, unread messages, reviews waiting)
- Month calendar of bookings, business reports with charts (bookings, revenue, top services, gardeners)
- CSV export (opens in Excel), activity log of every change
- Blog and FAQ editor
- Manage services, team members and project gallery (with photo upload)
- Booking list with search, status/date filters and pagination; approve, reject, mark done, assign a team member
- Contact messages inbox
- Approve which customer reviews appear on the home page
- Manage users: block/unblock, promote/demote, approve admin requests, delete

**Security**
- Admin panel is only open to approved, non-blocked admins
- Every action that changes data uses POST/DELETE with CSRF protection
- Login, signup, booking and contact forms are rate-limited
- Blocked users are logged out on their next click
- Contact form has a hidden spam trap

## Tech stack

Laravel 12 · PHP 8.2+ · MySQL (or SQLite) · Blade · Bootstrap 5 · Tailwind (auth pages) · jQuery · SweetAlert · Pest

## Getting started

```bash
git clone https://github.com/mahamud894/Lawn-Care-Website---Gardener.git
cd Lawn-Care-Website---Gardener

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_DATABASE=gardener
DB_USERNAME=root
DB_PASSWORD=

# Who receives new booking / contact e-mails
ADMIN_EMAIL=you@example.com

# The main admin account created by the seeder
SEED_ADMIN_EMAIL=you@example.com
SEED_ADMIN_PASSWORD=choose-a-strong-password
```

Then:

```bash
php artisan migrate
php artisan db:seed      # creates the main admin
php artisan serve
```

Open http://127.0.0.1:8000 and log in with the admin e-mail/password from `.env`.

### Sending real e-mails

By default `MAIL_MAILER=log`, so e-mails are written to `storage/logs/laravel.log` instead of being sent. To send real e-mails, fill in the SMTP settings, for example Gmail with an [app password](https://support.google.com/accounts/answer/185833):

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=you@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=you@gmail.com
```

If mail is not set up, bookings and contact messages still work — the e-mail is just skipped and logged.

### Optional services

All of these are off by default and switch on when you fill in `.env` (see `.env.example`):

| Feature | Settings |
| --- | --- |
| Online payment | `SSLCOMMERZ_STORE_ID`, `SSLCOMMERZ_STORE_PASSWORD`, `SSLCOMMERZ_SANDBOX` |
| SMS | `SMS_DRIVER=bulksmsbd`, `SMS_API_KEY`, `SMS_SENDER_ID` |
| Google login | `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` (redirect URI: `APP_URL/auth/google/callback`) |
| Business details | `BUSINESS_NAME`, `CURRENCY`, `CURRENCY_SYMBOL`, `WHATSAPP_NUMBER` |

For SSLCommerz, set your site's IPN URL in their panel to `APP_URL/payment/sslcommerz/ipn`.

## Upgrading an existing database

If you already have the site running, just run:

```bash
php artisan migrate
```

The new migrations only add what is missing, so existing data is kept. Existing services automatically get a page URL from their title.

## Deploying to Render (Docker)

This repository ships with a `Dockerfile` and a `render.yaml` blueprint.

1. In Render: **New → Blueprint**, pick this repository, and confirm.
2. Fill in the values it asks for: `APP_URL`, `SEED_ADMIN_EMAIL`, `SEED_ADMIN_PASSWORD`,
   and your mail settings. `APP_KEY` can be left empty — one is generated on the
   first boot and kept on the disk.
3. Deploy. On the first boot the app creates its tables and the main admin account,
   then starts on the port Render provides.

**Why the `starter` plan.** Photos uploaded through the site are files, and the
database is a file. Free Render services have no disk, so those files are erased
whenever the service restarts (which also happens after 15 minutes of no traffic).
The blueprint therefore mounts a disk at `/data` and keeps uploads, the SQLite
database, sessions and logs there. For a free demo instead, set `plan: free`,
delete the `disk:` block, and add `SEED_DEMO_CONTENT=true` so the site refills
itself with sample content each time it restarts.

Want MySQL or Postgres instead of SQLite? Set `DB_CONNECTION`, `DB_HOST`,
`DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD` in the environment variables;
the disk is then only used for uploads.

Running one-off commands (Render → Shell, paid plans):

```bash
php artisan images:optimize     # shrink images uploaded before this feature existed
php artisan db:seed --class="Database\Seeders\DemoSeeder" --force
```

The same image runs anywhere Docker runs (Fly.io, Railway, a VPS, Coolify):

```bash
docker build -t gardener .
docker run -p 8080:8080 -e PORT=8080 -v gardener-data:/data gardener
```

### Vercel is not a fit for this app

Vercel runs PHP as serverless functions with a read-only filesystem, so uploaded
photos cannot be saved, and it has no database of its own. Running this site there
would mean rewriting uploads to go to S3 and renting a database elsewhere. Render,
a VPS, or ordinary cPanel hosting are all simpler and cheaper for this project.

## Going live (shared hosting)

On cPanel or a VPS, run these on the server after every update:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan optimize          # caches config, routes, views and events
php artisan images:optimize   # shrinks any large images already uploaded
```

In `.env` on the server:

```env
APP_ENV=production
APP_DEBUG=false
CACHE_STORE=file      # or redis
SESSION_DRIVER=file   # or redis
```

Speed features already built in:

- Uploaded photos are resized and saved as WebP automatically (a 5 MB phone photo becomes ~100–200 KB)
- Images below the screen load only when scrolled to; phones get a smaller hero image
- `public/.htaccess` turns on gzip compression and browser caching (Apache)
- Public lists (services, team, projects, reviews, FAQ) are cached and refresh automatically when you edit them in the admin panel
- Database indexes on the columns used for slot checks and filters

If you change a theme image in `public/site/img`, run `php artisan images:optimize` again.

## Running tests

```bash
php artisan test
```

## Project structure

| Path | What's there |
| --- | --- |
| `routes/web.php` | All routes, grouped into public / guest / customer / admin |
| `app/Http/Controllers` | Page logic (bookings, services, team, projects, reviews, contact, auth) |
| `app/Http/Middleware` | Admin-only access and blocked-user logout |
| `app/Notifications` | E-mails sent to customers and the admin |
| `resources/views/pages` | Public pages, customer dashboard and admin panel |
| `tests/Feature` | Pest tests |
