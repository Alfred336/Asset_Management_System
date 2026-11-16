# IT Asset Management System

A web application for managed service providers (MSPs) to keep a register of their client companies, the staff at each company, and the IT devices those staff use.

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4-4E56A6?style=flat-square&logo=livewire&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)

## Features

- **Companies, staff and devices:** create, edit, search and soft-delete records; assign devices to staff; record hardware details, warranty dates and status history.
- **Import and export:** bring devices in from Excel and export them again; download reports as PDF.
- **Dashboard:** device statistics, charts and upcoming warranty and status reminders.
- **Access control:** roles and permissions with Spatie Laravel Permission (Super Admin, Admin, Manager), and company-based access so users only see the companies assigned to them.
- **Authentication:** Laravel Fortify with email verification, password reset and two-factor authentication.
- **Scheduled jobs:**

  | Job | Schedule |
  | --- | --- |
  | Warranty-expiry check and notifications | daily |
  | Status-update reminders to technicians | daily at 08:00 |
  | Weekly device report | Fridays at 16:00 |
  | Database backup | daily at midnight |

## Tech stack

| Layer | Technology |
| --- | --- |
| Backend | PHP 8.4, Laravel 13, Laravel Fortify, Laravel Sanctum |
| Frontend | Livewire, Volt, Flux UI, Tailwind CSS, Chart.js, Vite |
| Data | SQLite for development; MySQL/MariaDB or PostgreSQL in production |
| Documents | DomPDF (PDF reports), Laravel Excel (import/export) |
| Operations | Docker Compose, Nginx, Supervisor, Redis, cron |
| Testing | Pest |

## Getting started

Requirements: PHP 8.4, Composer, Node.js and npm.

```bash
git clone https://github.com/Alfred336/Asset_Management_System.git
cd Asset_Management_System
composer run setup
php artisan db:seed
composer run dev
```

`composer run setup` installs dependencies, creates `.env`, generates the app key, runs migrations and builds the frontend. `php artisan db:seed` creates the roles and permissions, a Super Admin user and sample data; change that user's password before using the system for real data.

### With Docker

```bash
docker compose up -d --build
```

The app is then served by Nginx at `http://localhost:8080`, with a separate container running the queue worker.

## Deployment

The `scripts/` folder contains helper scripts for a Debian server:

| Script | Purpose |
| --- | --- |
| `bootstrap-debian-server.sh` | Base server preparation |
| `setup-postgres-debian.sh`, `setup-redis-debian.sh` | Database and cache services |
| `install-nginx-site-debian.sh` | Nginx site configuration |
| `install-supervisor-debian.sh` | Queue workers under Supervisor |
| `backup-db.sh`, `setup-backup-scheduler.sh` | Database backups (SQLite, MySQL/MariaDB, PostgreSQL) |

Read each script before running it on a server. Backup set-up is described in [docs/backup.md](docs/backup.md) and [BACKUP_SETUP_QUICK.md](BACKUP_SETUP_QUICK.md).

The scheduled jobs need Laravel's scheduler running every minute:

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Testing

```bash
php artisan test
```

## Author

Alfred Festo — [github.com/Alfred336](https://github.com/Alfred336)
