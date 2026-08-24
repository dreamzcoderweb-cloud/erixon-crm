<?php

use App\Http\Controllers\Api\StaffAuthController;
// use App\Http\Controllers\Api\SlotController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('staff/login', [StaffAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [StaffAuthController::class, 'me']);
        Route::post('logout', [StaffAuthController::class, 'logout']);

        Route::get('credit-requests', [\App\Http\Controllers\CreditRequestController::class, 'listData']);
        Route::post('credit-request', [\App\Http\Controllers\CreditRequestController::class, 'store']);
        Route::get('customers/search', [\App\Http\Controllers\CustomerController::class, 'search']);
    });

    // Public / External API route for credit request submission
    Route::post('credit-request', [\App\Http\Controllers\CreditRequestController::class, 'store']);
    Route::get('credit-requests', [\App\Http\Controllers\CreditRequestController::class, 'listData']);
});

