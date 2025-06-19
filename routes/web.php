<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\TehsilController;
use App\Http\Controllers\CompanyController; 

// Redirect root URL directly to admin login page
Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::prefix('admin')->group(function () {

    // Public routes
    Route::get('login', [AdminController::class, 'create'])->name('admin.login');
    Route::post('login', [AdminController::class, 'store'])->name('auth.login.request');

    // Admin Logout
    Route::get('logout', [AdminController::class, 'destroy'])->name('admin.logout');

    // Protected routes using 'admin' and 'last_seen' middleware
    Route::middleware(['admin', 'last_seen'])->group(function () {

        // Dashboard
        Route::get('dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

        /**
         * ✨ Multi-Tenant Protected Routes
         * These routes are now also protected by 'company.access' middleware
         * to restrict access based on authenticated user's company.
         */
        Route::middleware(['company.access'])->group(function () {
            // Roles CRUD
            Route::resource('roles', RoleController::class);

            // Permissions CRUD
            Route::resource('permissions', PermissionController::class);

            // Users CRUD
            Route::resource('users', UserController::class);
            Route::post('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');

            // Users Company
            Route::resource('companies', CompanyController::class);
            Route::patch('companies/{id}/toggle', [CompanyController::class, 'toggle'])->name('companies.toggle');


            // Customers CRUD
            Route::resource('customers', CustomerController::class);
            Route::patch('customers/{id}/toggle', [CustomerController::class, 'toggle'])->name('customers.toggle');
        });

        // Dropdowns
        Route::resource('states', StateController::class);
        Route::resource('districts', DistrictController::class);
        Route::resource('cities', CityController::class);
        Route::resource('tehsils', TehsilController::class);

        // For AJAX dropdown loading
        Route::get('/get-districts/{state_id}', [UserController::class, 'getDistricts'])->name('get.districts');
        Route::get('/get-cities/{district_id}', [UserController::class, 'getCities'])->name('get.cities');
        Route::get('/get-tehsils/{city_id}', [UserController::class, 'getTehsils'])->name('get.tehsils');
        Route::get('/get-pincodes/{city_id}', [UserController::class, 'getPincodes'])->name('get.pincodes');
    });
});
