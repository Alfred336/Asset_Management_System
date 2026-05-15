# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Setup
```bash
# Install PHP and JS dependencies, setup environment, and build assets
composer run setup
```

### Development Server
```bash
# Start Laravel development server, queue worker, log watcher, and Vite dev server
composer run dev
```

### Asset Building
```bash
# Build production assets
npm run build

# Start Vite development server (for hot module replacement)
npm run dev
```

### Linting
```bash
# Run Pint (PHP code style fixer) in parallel
composer run lint

# Check for linting issues without fixing
composer run lint:check
```

### Testing
```bash
# Run full test suite (includes lint check and PHPUnit/Pest tests)
composer run test

# Run only PHPUnit/Pest tests
php artisan test
```

### Database
```bash
# Run migrations
php artisan migrate

# Rollback the last migration
php artisan migrate:rollback

# Reset and re-run all migrations
php artisan migrate:refresh

# Seed the database
php artisan db:seed
```

### Artisan
```bash
# Clear configuration cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Clear application cache
php artisan cache:clear
```

## Code Architecture

### Directory Structure
- **app/** - Core application logic
  - **Actions/** - Fortify authentication actions (user creation, password reset)
  - **Concerns/** - Reusable trait-like classes (validation rules)
  - **Http/Controllers/** - Base controller and API controllers
  - **Livewire/** - Livewire components for reactive UI
    - **Actions/** - Logout action component
    - **Settings/** - User settings components (profile, appearance, security)
  - **Models/** - Eloquent models (User)
  - **Providers/** - Service providers (AppServiceProvider, FortifyServiceProvider)
- **bootstrap/** - Laravel framework bootstrapping files
- **config/** - Configuration files (app, database, fortify, etc.)
- **database/** - Database migrations, factories, seeders, and SQLite database file
- **public/** - Compiled assets (built by Vite)
- **resources/** - Uncompiled assets and views
  - **views/** - Blade templates
    - **layouts/** - Application layouts (app, auth) and components (sidebar, header)
    - **partials/** - Shared template parts (head)
    - **components/** - Blade components
  - **css/** and **js/** - Vite entry points (app.css, app.js)
- **routes/** - Web route definitions
  - **web.php** - Traditional routes (home, dashboard)
  - **settings.php** - Livewire routes for user settings
- **tests/** - Feature and unit tests

### Key Technologies
- **Laravel 13** - PHP framework
- **Livewire 4** - Full-stack reactive framework for Laravel
- **Flux UI** - Laravel-specific UI components built on Tailwind CSS
- **Fortify** - Laravel authentication backend
- **Vite** - Frontend build tool
- **Tailwind CSS 4** - Utility-first CSS framework
- **Pest** - Testing framework
- **Pint** - Laravel-specific PHP code style fixer

### Authentication
- Uses Laravel Fortify for authentication backend
- Livewire components handle the frontend authentication UI
- Two-factor authentication is supported (see migration adding 2FA columns to users table)
- Routes are protected by `auth` and `verified` middleware

### Styling
- Tailwind CSS 4 configured via Vite plugin
- Custom CSS in `resources/css/app.css`
- Dark mode support through CSS classes and Tailwind dark variant
- Flux UI components provide pre-styled interactive elements

### Database Schema
- Users table includes standard Laravel fields plus two-factor authentication columns:
  - `two_factor_secret` (encrypted)
  - `two_factor_recovery_codes` (encrypted)
  - `two_factor_confirmed_at` (timestamp)
- Standard Laravel tables for password reset tokens, sessions, cache, jobs

### Testing Approach
- Pest testing framework with Laravel plugin
- Tests located in `tests/Feature` and `tests/Unit`
- Test suite includes linting checks before running tests
- Uses SQLite in-memory database for testing (configured in php.xml)

## Common Tasks

### Adding a New Livewire Component
1. Create a new class in `app/Livewire/` (or subdirectory)
2. Create a corresponding Blade view in `resources/views/livewire/` (optional if using inline render)
3. Register the route in `routes/settings.php` if it's a settings page
4. Add navigation item to sidebar/header layouts if needed

### Adding a New Feature
1. Create migration: `php artisan make:migration add_xxx_to_users_table`
2. Update User model if needed (casts, accessors, mutators)
3. Create Livewire component for UI
4. Add routes in appropriate route file
5. Update navigation in layouts
6. Write tests in `tests/Feature`

### Working with Assets
- Edit `resources/css/app.css` for custom CSS
- Edit `resources/js/app.js` for custom JavaScript
- Run `npm run dev` for hot reload during development
- Run `npm run build` for production builds

### Debugging
- Laravel Telescope is not installed by default; use Laravel's logging (`Log::facade`)
- Livewire provides debug mode with `wire:debug` attribute
- View compiled Blade templates in `storage/framework/views`