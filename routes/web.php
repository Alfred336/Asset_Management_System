<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Company Management
    Route::livewire('/companies', 'company-management')
        
        ->name('companies.index');

    // Staff Management
    Route::livewire('/staff', 'staff-management')
        ->middleware(['permission:view-staff'])
        ->name('staff.index');
});

require __DIR__.'/settings.php';