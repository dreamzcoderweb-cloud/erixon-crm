<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GeneralSettingController;
use App\Http\Controllers\ReferralSettingController;

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;

// Root admin route redirect
Route::get('/', function () {
    return Auth::check() ? redirect()->route('admin.dashboard') : redirect('/');
});

// login route
Route::match(['get', 'post'], 'login', [AuthController::class, 'login'])->name('login');
Route::match(['get', 'post'], 'logout', [AuthController::class, 'logout']);

Route::middleware(['auth', 'auth.session'])->group(function () {
    // dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    // profile
    Route::get('profile', [ProfileController::class, 'show'])
        ->middleware('permission:profile.view')
        ->name('profile.show');
    Route::post('profile/password', [ProfileController::class, 'updatePassword'])
        ->middleware('permission:profile.password')
        ->name('profile.password');


    // Role routes
    Route::get('roles_with_filter', [RoleController::class, 'index'])
        ->middleware('permission:roles.view')
        ->name('roles.index');
    Route::match(['get', 'post'], 'add_role', [RoleController::class, 'add'])->middleware('permission:roles.create');
    Route::match(['get', 'post'], 'edit_role/{id}', [RoleController::class, 'update'])->middleware('permission:roles.edit');
    Route::post('delete_role/{id}', [RoleController::class, 'delete'])->middleware('permission:roles.delete');

    // Staff routes
    Route::get('staff', [StaffController::class, 'index'])
        ->middleware('permission:staff.view')
        ->name('staff.index');
    Route::match(['get', 'post'], 'add_staff', [StaffController::class, 'add'])->middleware('permission:staff.create');
    Route::match(['get', 'post'], 'edit_staff/{id}', [StaffController::class, 'update'])->middleware('permission:staff.edit');
    Route::post('delete_staff/{id}', [StaffController::class, 'delete'])->middleware('permission:staff.delete');

    //Customer routes
    Route::get('customers', [CustomerController::class, 'index'])
        ->middleware('permission:customers.view')
        ->name('customers.index');

    // Settings routes
    Route::get('settings/general', [GeneralSettingController::class, 'index'])
        ->middleware('permission:general-settings.view')
        ->name('settings.general');
    Route::post('settings/general', [GeneralSettingController::class, 'update'])
        ->middleware('permission:general-settings.edit')
        ->name('settings.general.update');

    Route::get('settings/referral', [ReferralSettingController::class, 'index'])
        ->middleware('permission:referral-settings.view')
        ->name('settings.referral');
    Route::post('settings/referral', [ReferralSettingController::class, 'update'])
        ->middleware('permission:referral-settings.edit')
        ->name('settings.referral.update');

});
