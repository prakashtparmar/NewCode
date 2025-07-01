<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiTripController;

// Existing Auth API routes
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/register', [ApiAuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/profile', [ApiAuthController::class, 'profile']);
    Route::post('/change-password', [ApiAuthController::class, 'changePassword']);

    // ✅ New Trip API routes (appended here)
    Route::post('/trips/store', [ApiTripController::class, 'storeTrip']);
    Route::post('/trips/log-point', [ApiTripController::class, 'logPoint']);
    Route::get('/trips/{tripId}/logs', [ApiTripController::class, 'logs']);
    Route::post('/trips/{tripId}/complete', [ApiTripController::class, 'completeTrip']);
});
