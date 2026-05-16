# MSP IT Asset Management System

## 📌 Project Overview

This repository is a **Laravel 13 + Livewire IT asset and staff management system** built with:

- Laravel 13
- Laravel Fortify authentication
- Livewire + Flux UI
- Tailwind CSS
- Soft deletes for key resources
- Livewire-based CRUD for companies, staff, users, and devices

The codebase is designed for MSP-style multi-company asset management and internal user administration.

---

## 🏢 Business Purpose

This system supports:

- Managing multiple client companies
- Tracking IT devices and hardware assets
- Maintaining staff records per company
- Managing application users and account status
- Assigning devices to staff members
- Tracking device inventory, warranty, and hardware details

---

## 👤 Authentication & Authorization

The system uses **Laravel Fortify** for authentication with:

- Login
- Registration
- Email verification
- Password reset
- Two-factor authentication

Authorization is scaffolded using Gate checks inside Livewire components.

---

## 🧠 Implemented Modules

### 1. User Management
- Create, edit, delete, and restore users
- Soft delete users for activated/inactivated state
- Search, sort, and pagination
- Permission gate checks for create/edit/delete actions

---

### 2. Company Management
- Create, edit, delete companies
- Track company contact details, website, tax ID, and status
- Soft delete support
- Search, sort, and pagination

---

### 3. Staff Management
- Create, edit, delete staff members
- Assign staff to companies
- Track staff details including position, hire date, salary, employment type, status, and notes
- Filter by company
- Search, sort, and pagination

---

### 4. Device Management
- Create, edit, delete device records
- Assign devices to companies and staff members
- Track hardware details:
  - Asset tag
  - Serial number
  - Model and manufacturer
  - Device type
  - Operating system and version
  - Processor, RAM, storage
  - IP address, MAC address, hostname
  - Location, purchase date, purchase cost, warranty expiry
  - Status values: active, offline, online, formatted, dead, under_repair, retired
- Search, sort, filters, and pagination

---

## 🔧 Current Implementation Notes

- The repo currently includes device inventory support, but the `devices` route is not yet exposed in `routes/web.php`.
- There is no `Spatie Laravel Permission` package installed in the current `composer.json`.
- Weekly PDF report generation and automatic reminder notifications are not implemented in the current codebase.
- Task tracking / ticket replacement is not present in the current repository.

---

## 📁 Folder Structure

- `app/`
  - `Actions/Fortify/`
  - `Concerns/`
  - `Http/Controllers/`
  - `Livewire/`
    - `Actions/`
    - `Settings/`
    - `users/`
    - `CompanyManagement.php`
    - `DeviceManagement.php`
    - `StaffManagement.php`
    - `UserManagement.php`
  - `Models/`
  - `Providers/`
- `bootstrap/`
- `config/`
- `database/`
  - `factories/`
  - `migrations/`
  - `seeders/`
- `public/`
- `resources/`
  - `css/`
  - `js/`
  - `views/`
- `routes/`
  - `console.php`
  - `settings.php`
  - `web.php`
- `storage/`
- `tests/`
- `vendor/`

---

## 🧪 Testing & Tooling

- `pestphp/pest` for tests
- `laravel/pint` for code style
- `laravel/sail` available for local environment containers
- `npm` assets via Vite
