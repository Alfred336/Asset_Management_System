<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Company Management
    Route::livewire('/companies', 'company-management')
        ->middleware(['permission:view-companies'])
        ->name('companies.index');
});

require __DIR__.'/settings.php';