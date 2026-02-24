<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:faculty'])
    ->prefix('faculty')
    ->name('faculty.')
    ->group(function () {
        Route::livewire('/dashboard', 'pages::faculty.dashboard')->name('dashboard');
    });
