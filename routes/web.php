<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

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
});

require __DIR__.'/settings.php';
