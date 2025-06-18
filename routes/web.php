<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

// Redirect root URL directly to admin login page
Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::prefix('admin')->group(function () {

    // Public routes (still behind 'web' middleware implicitly)
    Route::get('login', [AdminController::class, 'create'])->name('admin.login');
    Route::post('login', [AdminController::class, 'store'])->name('auth.login.request');

    // Admin Logout
    Route::get('logout', [AdminController::class, 'destroy'])->name('admin.logout');

    // Protected routes using 'admin' middleware and 'last_seen' middleware
    Route::middleware(['admin', 'last_seen'])->group(function () {

        // Display Dashboard Page On Success Login
        Route::get('dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        
        // Roles CRUD
        Route::resource('roles', RoleController::class);

        // Permissions CRUD
        Route::resource('permissions', PermissionController::class);

        // Users CRUD
        Route::resource('users', UserController::class);

        // Customers CRUD
        Route::resource('customers', CustomerController::class);

        // Toggle Route (for activating/deactivating users)
        Route::post('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        
    });
});
