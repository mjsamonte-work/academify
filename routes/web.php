<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::resource('admin/users', UserController::class)
        ->except(['destroy'])
        ->middleware('permission:users.view')
        ->names('admin.users');
});

require __DIR__.'/settings.php';
