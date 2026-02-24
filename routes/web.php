<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;

// GUEST ROUTES
Route::middleware('guests')->group(function () {
    Route::view('/', 'auth.login')->name('login');
    Route::redirect('/login', '/');

    Route::controller(GoogleAuthController::class)->group(function () {
        Route::get('/auth/google/redirect', 'redirect')->name('google.redirect');
        Route::get('/auth/google/callback', 'callback')->name('google.callback');
    });
});

Route::post('/logout', [GoogleAuthController::class, 'logout'])
    ->name('logout');

require __DIR__.'/admin.php';
require __DIR__.'/faculty.php';
