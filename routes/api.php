<?php

use App\Http\Controllers\Api\StaffAuthController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\LeadApiController;

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('staff/login', [StaffAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [StaffAuthController::class, 'me']);
        Route::post('logout', [StaffAuthController::class, 'logout']);

        Route::get('credit-requests', [\App\Http\Controllers\CreditRequestController::class, 'listData']);
        Route::post('credit-request', [\App\Http\Controllers\CreditRequestController::class, 'store']);
        Route::get('customers/search', [\App\Http\Controllers\CustomerController::class, 'search']);

        // Mobile App Customer Management & Additional Custom Fields
        Route::get('customers/form-data', [CustomerApiController::class, 'getFormData']);
        Route::get('customers/users', [CustomerApiController::class, 'getUsers']);
        Route::get('customers', [CustomerApiController::class, 'index']);
        Route::post('customers', [CustomerApiController::class, 'store']);
        Route::get('customers/edit/{id}', [CustomerApiController::class, 'edit']);
        Route::get('customers/{id}', [CustomerApiController::class, 'show']);
        Route::post('customers/update/{id}', [CustomerApiController::class, 'update']);
        Route::delete('customers/delete/{id}', [CustomerApiController::class, 'destroy']);
        Route::post('customers/change-status/{id}', [CustomerApiController::class, 'changeStatus']);

        // Mobile App Lead Management & Additional Custom Fields
        Route::get('leads/form-data', [LeadApiController::class, 'getFormData']);
        Route::get('leads', [LeadApiController::class, 'index']);
        Route::post('leads', [LeadApiController::class, 'store']);
        Route::get('leads/edit/{id}', [LeadApiController::class, 'edit']);
        Route::get('leads/{id}', [LeadApiController::class, 'show']);
        Route::post('leads/update/{id}', [LeadApiController::class, 'update']);
        Route::put('leads/{id}', [LeadApiController::class, 'update']);
        Route::delete('leads/delete/{id}', [LeadApiController::class, 'destroy']);
        Route::delete('leads/{id}', [LeadApiController::class, 'destroy']);
        Route::post('leads/change-status/{id}', [LeadApiController::class, 'changeStatus']);
    });

    // Public / External API route for credit request submission
    Route::post('credit-request', [\App\Http\Controllers\CreditRequestController::class, 'store']);
    Route::get('credit-requests', [\App\Http\Controllers\CreditRequestController::class, 'listData']);
});

