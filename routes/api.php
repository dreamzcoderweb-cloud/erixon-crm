<?php

use App\Http\Controllers\Api\StaffAuthController;
// use App\Http\Controllers\Api\SlotController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('staff/login', [StaffAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [StaffAuthController::class, 'me']);
        Route::post('logout', [StaffAuthController::class, 'logout']);

        // Route::prefix('slot')->group(function () {
        //     Route::get('/', [SlotController::class, 'index']);
        // });
    });
});

