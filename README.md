# 🌿 Gardener — Lawn Care & Gardening Website

A full lawn care business website built with **Laravel 12**. Customers can browse services, book a visit for a specific date and time slot, track their bookings and leave reviews. The owner runs everything from an admin panel.

## Features

**For customers**
- Service, About, Projects and Contact pages
- Online booking with live time-slot availability (a slot can never be double-booked)
- Personal dashboard: track status and assigned gardener, cancel or reschedule, leave a star review after the job is done
- E-mail updates when a booking is received, approved, completed or assigned
- Sign up, login, and "forgot password" by e-mail

**For the admin**
- Dashboard with live counts (bookings by status, unread messages, reviews waiting)
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

## Upgrading an existing database

If you already have the site running, just run:

```bash
php artisan migrate
```

The new migrations only add what is missing (`isBlocked`, `address`, `team_member_id`, and the new `contact_messages`, `projects` and `reviews` tables), so existing data is kept.

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
