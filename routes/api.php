<?php

use App\Http\Controllers\Api\CustomerAuthController;
// use App\Http\Controllers\Api\SlotController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('customer/register', [CustomerAuthController::class, 'register']);
    Route::post('customer/login', [CustomerAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('customer/me', [CustomerAuthController::class, 'me']);
        Route::post('customer/logout', [CustomerAuthController::class, 'logout']);

        // Route::prefix('slot')->group(function () {
        //     Route::get('/', [SlotController::class, 'index']);
        // });
    });
});

