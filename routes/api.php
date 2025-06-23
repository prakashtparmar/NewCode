<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

// Public routes
Route::post('/register', [ApiController::class, 'register']);
Route::post('/login', [ApiController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User resource routes
    Route::get('/users', [ApiController::class, 'indexUsers']);
    Route::post('/users', [ApiController::class, 'storeUser']);
    Route::get('/users/{user}', [ApiController::class, 'showUser']);
    Route::put('/users/{user}', [ApiController::class, 'updateUser']);
    Route::delete('/users/{user}', [ApiController::class, 'deleteUser']);
    Route::patch('/users/{user}/toggle', [ApiController::class, 'toggleUser']);

    // Authenticated user profile & logout
    Route::get('/profile', [ApiController::class, 'profile']);
    Route::post('/logout', [ApiController::class, 'logout']);

    // Location routes
    Route::get('/states/{state}/districts', [ApiController::class, 'getDistricts']);
    Route::get('/districts/{district}/cities', [ApiController::class, 'getCities']);
    Route::get('/cities/{city}/tehsils', [ApiController::class, 'getTehsils']);
    Route::get('/cities/{city}/pincodes', [ApiController::class, 'getPincodes']);
});
