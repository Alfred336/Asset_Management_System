<?php

use App\Livewire\VerifyEmailCode;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Email verification with OTP code
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', VerifyEmailCode::class)
        ->name('verification.notice');

    Route::post('/email/verification-notification', function () {
        app(VerifyEmailCode::class)->sendCode();

        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->middleware(['permission:view-dashboard'])
        ->name('dashboard');

    // Device Status Manager
    Route::livewire('/technician', 'device-status-manager')
        ->middleware(['permission:view-devices'])
        ->name('technician.dashboard');

    // Company Management
    Route::livewire('/companies', 'company-management')
        ->middleware(['permission:view-companies'])
        ->name('companies.index');

    // Staff Management
    Route::livewire('/staff', 'staff-management')
        ->middleware(['permission:view-staff'])
        ->name('staff.index');

    // Device Management
    Route::livewire('/devices', 'device-management')
        ->middleware(['permission:view-devices'])
        ->name('devices.index');

    // User Management
    Route::livewire('/users', 'user-management')
        ->middleware(['permission:view-users'])
        ->name('users.index');

    // Role Management
    Route::livewire('/roles', 'role-management')
        ->middleware(['permission:view-roles'])
        ->name('roles.index');
});

require __DIR__.'/settings.php';
