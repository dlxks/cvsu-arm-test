<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::livewire('/dashboard', 'pages::admin.dashboard')->name('dashboard');
    });
