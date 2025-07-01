<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\TripLogController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\TravelModeController;
use App\Http\Controllers\TourTypeController;
use App\Http\Controllers\PurposeController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\TehsilController;

/**
 * ✅ Public API routes
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public dropdown values API (used in mobile apps)
Route::get('/dropdown-values/{type}', [TripController::class, 'getDropdownValues']);

/**
 * ✅ Protected API routes (auth:sanctum middleware)
 */
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Users
    Route::get('/users', [UserController::class, 'indexUsers']);
    Route::post('/users', [UserController::class, 'storeUser']);
    Route::get('/users/{id}', [UserController::class, 'showUser']);
    Route::put('/users/{user}', [UserController::class, 'updateUser']);
    Route::delete('/users/{user}', [UserController::class, 'deleteUser']);
    Route::post('/users/{user}/toggle', [UserController::class, 'toggleUser']);
    Route::get('/profile', [UserController::class, 'profile']);

    // Companies
    Route::apiResource('/companies', CompanyController::class);
    Route::patch('/companies/{id}/toggle', [CompanyController::class, 'toggle']);

    // Customers
    Route::apiResource('/customers', CustomerController::class);
    Route::patch('/customers/{id}/toggle', [CustomerController::class, 'toggleStatus']);
    Route::delete('/customers/bulk-delete', [CustomerController::class, 'bulkDelete']);

    // Get executives for company (used in dropdowns)
    Route::get('/companies/{id}/executives', [CustomerController::class, 'getExecutives']);

    // Trips
    Route::apiResource('/trips', TripController::class);
    Route::post('/trips/{id}/approve', [TripController::class, 'approve']);
    Route::post('/trips/{id}/complete', [TripController::class, 'completeTrip']);
    Route::post('/trips/{id}/toggle-status', [TripController::class, 'toggleStatus']);
    Route::post('/trips/{id}/update-coordinates', [TripController::class, 'updateTripCoordinates']);
    Route::get('/trips/{trip}/map', [TripController::class, 'showRoute']);
    Route::get('/trips/{trip}/logs', [TripController::class, 'logs']);

    // Trip Logs
    Route::post('/trip-logs', [TripLogController::class, 'logPoint']);
    Route::get('/trip-logs/{tripId}', [TripLogController::class, 'logs']);
    Route::get('/trip-logs/{tripId}/distance', [TripLogController::class, 'calculateDistanceFromLogs']);

    // Travel Master Data
    Route::apiResource('/travelmodes', TravelModeController::class);
    Route::apiResource('/tourtypes', TourTypeController::class);
    Route::apiResource('/purposes', PurposeController::class);

    // HR Designations
    Route::apiResource('/hr/designations', DesignationController::class);

    // Location (Cascading Dropdown APIs)
    Route::apiResource('/states', StateController::class);
    Route::apiResource('/districts', DistrictController::class);
    Route::apiResource('/cities', CityController::class);
    Route::apiResource('/tehsils', TehsilController::class);

    Route::get('/get-districts/{state_id}', [UserController::class, 'getDistricts']);
    Route::get('/get-cities/{district_id}', [UserController::class, 'getCities']);
    Route::get('/get-tehsils/{city_id}', [UserController::class, 'getTehsils']);
    Route::get('/get-pincodes/{city_id}', [UserController::class, 'getPincodes']);
});
