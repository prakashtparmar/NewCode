<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\TripLogController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('locations', [LocationController::class, 'index']);
// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('/userDetail', [AuthController::class, 'userDetail']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // User
    Route::get('/users', [UserController::class, 'indexUsers']);
    Route::post('/users', [UserController::class, 'storeUser']);
    Route::get('/users/{id}', [UserController::class, 'showUser']);
    Route::put('/users/{user}', [UserController::class, 'updateUser']);
    Route::delete('/users/{user}', [UserController::class, 'deleteUser']);
    Route::post('/users/{user}/toggle', [UserController::class, 'toggleUser']);
    Route::get('/profile', [UserController::class, 'profile']);

    // Location
    Route::get('/districts/{state_id}', [LocationController::class, 'getDistricts']);
    Route::get('/cities/{district_id}', [LocationController::class, 'getCities']);
    Route::get('/tehsils/{city_id}', [LocationController::class, 'getTehsils']);
    Route::get('/pincodes/{city_id}', [LocationController::class, 'getPincodes']);

    // Trips
    Route::get('/trips', [TripController::class, 'index']);
    Route::post('/trips', [TripController::class, 'store']);
    Route::get('/trips/{id}', [TripController::class, 'show']);
    Route::put('/trips/{id}', [TripController::class, 'update']);
    Route::delete('/trips/{id}', [TripController::class, 'destroy']);
    Route::post('/trips/{id}/approve', [TripController::class, 'approve']);
    Route::post('/trips/{id}/update-coordinates', [TripController::class, 'updateTripCoordinates']);
    Route::get('/trip/active}', [TripController::class, 'lastActive']);

    // Trip Logs
    Route::post('/trip-logs', [TripLogController::class, 'logPoint']);
    Route::get('/trip-logs/{tripId}', [TripLogController::class, 'logs']);
    Route::get('/trip-logs/{tripId}/distance', [TripLogController::class, 'calculateDistanceFromLogs']);
});
