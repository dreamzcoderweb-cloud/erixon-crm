<?php

use App\Http\Controllers\Api\StaffAuthController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\LeadApiController;
use App\Http\Controllers\Api\FollowupApiController;
use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\CallLogApiController;
use App\Http\Controllers\Api\DemoProcessApiController;
use App\Http\Controllers\Api\CreditRequestApiController;

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('staff/login', [StaffAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [StaffAuthController::class, 'me']);
        Route::get('staff/menus', [StaffAuthController::class, 'menuAccess']);
        Route::post('logout', [StaffAuthController::class, 'logout']);
        Route::post('update-fcm-token', [StaffAuthController::class, 'updateFcmToken']);
        Route::post('staff/fcm-token', [StaffAuthController::class, 'updateFcmToken']);

        Route::get('customers/search', [CustomerApiController::class, 'search']);

        // Mobile App Credit Request Management & Additional Custom Fields
        Route::get('credit-requests/form-data', [CreditRequestApiController::class, 'getFormData']);
        Route::get('credit-requests', [CreditRequestApiController::class, 'index']);
        Route::post('credit-requests', [CreditRequestApiController::class, 'store']);
        Route::post('credit-request', [CreditRequestApiController::class, 'store']);
        Route::get('credit-requests/edit/{id}', [CreditRequestApiController::class, 'edit']);
        Route::get('credit-requests/{id}', [CreditRequestApiController::class, 'show']);
        Route::post('credit-requests/update/{id}', [CreditRequestApiController::class, 'update']);
        Route::delete('credit-requests/delete/{id}', [CreditRequestApiController::class, 'destroy']);
        Route::post('credit-requests/approve-admin/{id}', [CreditRequestApiController::class, 'approveAdmin']);
        Route::post('credit-requests/approve-support/{id}', [CreditRequestApiController::class, 'approveSupport']);
        Route::post('credit-requests/reject/{id}', [CreditRequestApiController::class, 'reject']);
        Route::post('credit-requests/change-status/{id}', [CreditRequestApiController::class, 'changeStatus']);

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
        Route::delete('leads/delete/{id}', [LeadApiController::class, 'destroy']);
        Route::post('leads/change-status/{id}', [LeadApiController::class, 'changeStatus']);

        // Mobile App Follow-up Management & Additional Custom Fields
        Route::get('followups/form-data', [FollowupApiController::class, 'getFormData']);
        Route::get('followups/today-reminders', [FollowupApiController::class, 'getTodayReminders']);
        Route::get('followups', [FollowupApiController::class, 'index']);
        Route::post('followups', [FollowupApiController::class, 'store']);
        Route::get('followups/edit/{id}', [FollowupApiController::class, 'edit']);
        Route::get('followups/{id}', [FollowupApiController::class, 'show']);
        Route::post('followups/update/{id}', [FollowupApiController::class, 'update']);
        Route::delete('followups/delete/{id}', [FollowupApiController::class, 'destroy']);
        Route::post('followups/change-status/{id}', [FollowupApiController::class, 'changeStatus']);

        // Mobile App Attendance Management (Check-in, Check-out, Today Status, History)
        Route::get('attendance/today', [AttendanceApiController::class, 'todayStatus']);
        Route::post('attendance/check-in', [AttendanceApiController::class, 'checkIn']);
        Route::post('attendance/check-out', [AttendanceApiController::class, 'checkOut']);
        Route::get('attendance/history', [AttendanceApiController::class, 'history']);

        // Mobile App Call Log & Call Recording Management
        Route::get('call-report/pdf', [CallLogApiController::class, 'exportPdf']);
        Route::get('call-report', [CallLogApiController::class, 'report']);
        Route::get('call-logs', [CallLogApiController::class, 'index']);
        Route::post('call-logs', [CallLogApiController::class, 'store']);
        Route::get('call-logs/{id}', [CallLogApiController::class, 'show']);
        Route::post('call-recordings', [CallLogApiController::class, 'store']);

        // Mobile App Demo Process Management
        Route::get('demo-processes/form-data', [DemoProcessApiController::class, 'getFormData']);
        Route::get('demo-processes', [DemoProcessApiController::class, 'index']);
        Route::post('demo-processes', [DemoProcessApiController::class, 'store']);
        Route::get('demo-processes/edit/{id}', [DemoProcessApiController::class, 'edit']);
        Route::get('demo-processes/{id}', [DemoProcessApiController::class, 'show']);
        Route::post('demo-processes/update/{id}', [DemoProcessApiController::class, 'update']);
        Route::put('demo-processes/{id}', [DemoProcessApiController::class, 'update']);
        Route::delete('demo-processes/delete/{id}', [DemoProcessApiController::class, 'destroy']);
        Route::post('demo-processes/change-status/{id}', [DemoProcessApiController::class, 'changeStatus']);
    });

    // Direct / Browser PDF download route with token parameter
    Route::get('call-report/download', [CallLogApiController::class, 'exportPdf']);
});

