# MSP IT Asset Management System

## 📌 Project Overview

A **Laravel 13 + Livewire IT asset and staff management system** built for Managed Service Providers.

**Stack:**
- Laravel 13, Laravel Fortify authentication
- Livewire + Flux UI
- Tailwind CSS (Rose & Slate premium theme)
- Spatie Laravel Permission (RBAC)
- SQLite (dev) / MySQL (production)
- Pest for testing, Vite for assets

---

## 🏢 Business Purpose

- Managing multiple client companies
- Tracking IT devices and hardware assets
- Maintaining staff records per company
- Managing application users, roles, and permissions
- Assigning devices to staff members
- Tracking device inventory, warranty, and hardware details
- Automatic warranty expiry notifications

---

## 👤 Authentication & Authorization

Laravel Fortify with:
- Login, Registration, Email verification (`MustVerifyEmail`)
- Password reset, Two-factor authentication

Authorization via **Spatie Laravel Permission**:
- `Gate::before` grants Super Admin all permissions
- Roles: `Super Admin`, `Admin`, `Manager`
- Permissions: view/create/edit/delete for companies, staff, devices, users, roles

---

## ✅ Implemented Modules

### 1. User Management
- Create, edit, delete, restore users (soft delete)
- Assign roles directly from the user table
- Role displayed as badge in table
- Search, sort, pagination

### 2. Company Management
- Create, edit, delete companies (soft delete)
- Contact details, website, tax ID, status
- Search, sort, pagination

### 3. Staff Management
- Create, edit, delete staff (soft delete)
- Assign to companies, track position, hire date, salary, employment type
- Filter by company, search, sort, pagination

### 4. Device Management
- Full hardware inventory (asset tag, serial, model, manufacturer, type, OS, CPU, RAM, storage, IP, MAC, hostname, location)
- Status: active, offline, online, formatted, dead, under_repair, retired
- Device status history tracked via `DeviceStatusHistory` model
- Assign to company and staff
- Warranty expiry tracking
- Search, sort, filters, pagination

### 5. Role Management
- Create, edit, delete roles (Super Admin only)
- Assign permissions to roles
- Search, pagination

### 6. Dashboard
- Stats: total devices, staff, companies, expiring warranties (next 30 days)
- Upcoming warranty expiries table (next 60 days)
- Quick action links to all modules
- Notification bell

### 7. Notifications
- `DeviceWarrantyExpiryNotification` — sent to all users when a device warranty expires within 30 days
- `CheckDeviceWarrantyExpiry` artisan command — scheduled daily

---

## 🔧 Not Implemented

1. **Weekly PDF report generation** — no PDF library or scheduled report job
2. **Device status history UI** — model and migration exist, but no view to display history

---

## 📁 Key Folder Structure

```
app/
  Console/Commands/CheckDeviceWarrantyExpiry.php
  Livewire/
    CompanyManagement.php
    DeviceManagement.php
    StaffManagement.php
    UserManagement.php
    RoleManagement.php
  Models/
    Company.php, Staff.php, Device.php, DeviceStatusHistory.php, User.php
  Notifications/DeviceWarrantyExpiryNotification.php
database/migrations/   — companies, staff, devices, device_status_histories, permissions
resources/views/
  livewire/            — all CRUD views
  layouts/app/sidebar.blade.php
  dashboard.blade.php
  welcome.blade.php    — branded landing page
routes/web.php         — all routes behind auth + permission middleware
```
