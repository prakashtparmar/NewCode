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
use App\Http\Controllers\TripController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\TravelModeController;
use App\Http\Controllers\PurposeController;
use App\Http\Controllers\TourTypeController;

// Redirect root URL to admin login
Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::prefix('admin')->group(function () {

    // Public Routes
    Route::get('login', [AdminController::class, 'create'])->name('admin.login');
    Route::post('login', [AdminController::class, 'store'])->name('auth.login.request');
    Route::get('logout', [AdminController::class, 'destroy'])->name('admin.logout');

    // ✅ Public AJAX dropdown data route (before middleware)
    // Route::get('dropdown-values/{type}', [TripController::class, 'getDropdownValues'])->name('dropdown.values');

    // Protected Routes
    Route::middleware(['admin', 'last_seen'])->group(function () {

        // Dashboard
        Route::get('dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        // ✅ Add this here
    Route::get('users/{userId}/sessions', [AdminController::class, 'getUserSessionHistory'])->name('admin.users.sessions');
        Route::delete('/customers/bulk-delete', [CustomerController::class, 'bulkDelete'])->name('customers.bulk-delete');

        // Multi-Tenant Group
        Route::middleware(['company.access'])->group(function () {

            // Role, Permission, User Management
            Route::resource('roles', RoleController::class);
            Route::resource('permissions', PermissionController::class);
            Route::resource('users', UserController::class);
            Route::post('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');


            Route::resource('/hr/designations', DesignationController::class);


            Route::prefix('trips')->group(function () {
                Route::resource('travelmode', TravelModeController::class)->names('travelmode');
                Route::resource('tourtype', TourTypeController::class)->names('tourtype');
                Route::resource('purpose', PurposeController::class)->names('purpose');
            });





            // Companies
            Route::resource('companies', CompanyController::class);
            Route::patch('companies/{id}/toggle', [CompanyController::class, 'toggle'])->name('companies.toggle');

            // Customers
            Route::resource('customers', CustomerController::class);
            Route::patch('/customers/{id}/toggle', [CustomerController::class, 'toggleStatus'])->name('customers.toggle');

            // Trips
            Route::resource('trips', TripController::class);
            Route::post('/trips/{trip}/approve', [TripController::class, 'approve'])->name('trips.approve');

            // ✅ New: Mark trip complete
            // Route::post('/trips/{trip}/complete', [TripController::class, 'completeTrip'])->name('trips.complete');
            Route::post('/admin/trips/{id}/complete', [TripController::class, 'completeTrip'])->name('trips.complete');
            Route::post('/trips/{trip}/toggle-status', [TripController::class, 'toggleStatus'])->name('trips.status.toggle');


            // ✅ AJAX Executive Fetch by Company (used in create/edit customer)
            Route::get('companies/{id}/executives', [CustomerController::class, 'getExecutives'])->name('company.executives');
        });

        // Cascading Dropdowns (for location)
        Route::resource('states', StateController::class);
        Route::resource('districts', DistrictController::class);
        Route::resource('cities', CityController::class);
        Route::resource('tehsils', TehsilController::class);

        Route::get('/get-districts/{state_id}', [UserController::class, 'getDistricts'])->name('get.districts');
        Route::get('/get-cities/{district_id}', [UserController::class, 'getCities'])->name('get.cities');
        Route::get('/get-tehsils/{city_id}', [UserController::class, 'getTehsils'])->name('get.tehsils');
        Route::get('/get-pincodes/{city_id}', [UserController::class, 'getPincodes'])->name('get.pincodes');

        // For trip log point collection (can go in api.php if coming from mobile)
        Route::post('/trip-logs', [TripController::class, 'logPoint'])->name('trip.log');

        // For viewing trip route
        Route::get('/trips/{trip}/map', [TripController::class, 'showRoute'])->name('trip.map');
        Route::get('/trips/{trip}/logs', [TripController::class, 'logs'])->name('trips.logs');
    });
});
