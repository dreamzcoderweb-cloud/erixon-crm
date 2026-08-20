<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\LeadStageController;
use App\Http\Controllers\LeadRequirementController;
use App\Http\Controllers\LostReasonController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\GeneralSettingController;
use App\Http\Controllers\ReferralSettingController;
use App\Http\Controllers\LeadDocumentController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\CallRecordingController;
use App\Http\Controllers\CallLogController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;

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
    Route::post('profile/update', [ProfileController::class, 'updateProfile'])
        ->middleware('permission:profile.view')
        ->name('profile.update');
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
    Route::post('staff/toggle-leave/{id}', [StaffController::class, 'toggleLeave'])->middleware('permission:staff.edit');

    // Customer routes
    Route::get('customers', [CustomerController::class, 'index'])
        ->middleware('permission:customers.view')
        ->name('customers.index');
    Route::get('customers/data', [CustomerController::class, 'listData'])
        ->middleware('permission:customers.view');
    Route::post('customers/store', [CustomerController::class, 'store'])
        ->middleware('permission:customers.create');
    Route::get('customers/edit/{id}', [CustomerController::class, 'edit'])
        ->middleware('permission:customers.edit');
    Route::post('customers/update/{id}', [CustomerController::class, 'update'])
        ->middleware('permission:customers.edit');
    Route::delete('customers/delete/{id}', [CustomerController::class, 'destroy'])
        ->middleware('permission:customers.delete');
    Route::post('customers/change-status/{id}', [CustomerController::class, 'changeStatus'])
        ->middleware('permission:customers.edit');

    // Lead Sources routes
    Route::get('lead-sources', [LeadSourceController::class, 'index'])
        ->middleware('permission:lead-sources.view')
        ->name('lead-sources.index');
    Route::get('lead-sources/data', [LeadSourceController::class, 'listData'])
        ->middleware('permission:lead-sources.view');
    Route::post('lead-sources/store', [LeadSourceController::class, 'store'])
        ->middleware('permission:lead-sources.create');
    Route::get('lead-sources/edit/{id}', [LeadSourceController::class, 'edit'])
        ->middleware('permission:lead-sources.edit');
    Route::post('lead-sources/update/{id}', [LeadSourceController::class, 'update'])
        ->middleware('permission:lead-sources.edit');
    Route::delete('lead-sources/delete/{id}', [LeadSourceController::class, 'destroy'])
        ->middleware('permission:lead-sources.delete');
    Route::post('lead-sources/change-status/{id}', [LeadSourceController::class, 'changeStatus'])
        ->middleware('permission:lead-sources.edit');

    // Lead Stages routes
    Route::get('lead-stages', [LeadStageController::class, 'index'])
        ->middleware('permission:lead-stages.view')
        ->name('lead-stages.index');
    Route::get('lead-stages/data', [LeadStageController::class, 'listData'])
        ->middleware('permission:lead-stages.view');
    Route::post('lead-stages/store', [LeadStageController::class, 'store'])
        ->middleware('permission:lead-stages.create');
    Route::get('lead-stages/edit/{id}', [LeadStageController::class, 'edit'])
        ->middleware('permission:lead-stages.edit');
    Route::post('lead-stages/update/{id}', [LeadStageController::class, 'update'])
        ->middleware('permission:lead-stages.edit');
    Route::delete('lead-stages/delete/{id}', [LeadStageController::class, 'destroy'])
        ->middleware('permission:lead-stages.delete');
    Route::post('lead-stages/change-status/{id}', [LeadStageController::class, 'changeStatus'])
        ->middleware('permission:lead-stages.edit');

    // Lead Requirements routes
    Route::get('lead-requirements', [LeadRequirementController::class, 'index'])
        ->middleware('permission:lead-requirements.view')
        ->name('lead-requirements.index');
    Route::get('lead-requirements/data', [LeadRequirementController::class, 'listData'])
        ->middleware('permission:lead-requirements.view');
    Route::post('lead-requirements/store', [LeadRequirementController::class, 'store'])
        ->middleware('permission:lead-requirements.create');
    Route::get('lead-requirements/edit/{id}', [LeadRequirementController::class, 'edit'])
        ->middleware('permission:lead-requirements.edit');
    Route::post('lead-requirements/update/{id}', [LeadRequirementController::class, 'update'])
        ->middleware('permission:lead-requirements.edit');
    Route::delete('lead-requirements/delete/{id}', [LeadRequirementController::class, 'destroy'])
        ->middleware('permission:lead-requirements.delete');
    Route::post('lead-requirements/change-status/{id}', [LeadRequirementController::class, 'changeStatus'])
        ->middleware('permission:lead-requirements.edit');

    // Lost Reasons routes
    Route::get('lost-reasons', [LostReasonController::class, 'index'])
        ->middleware('permission:lost-reasons.view')
        ->name('lost-reasons.index');
    Route::get('lost-reasons/data', [LostReasonController::class, 'listData'])
        ->middleware('permission:lost-reasons.view');
    Route::post('lost-reasons/store', [LostReasonController::class, 'store'])
        ->middleware('permission:lost-reasons.create');
    Route::get('lost-reasons/edit/{id}', [LostReasonController::class, 'edit'])
        ->middleware('permission:lost-reasons.edit');
    Route::post('lost-reasons/update/{id}', [LostReasonController::class, 'update'])
        ->middleware('permission:lost-reasons.edit');
    Route::delete('lost-reasons/delete/{id}', [LostReasonController::class, 'destroy'])
        ->middleware('permission:lost-reasons.delete');
    Route::post('lost-reasons/change-status/{id}', [LostReasonController::class, 'changeStatus'])
        ->middleware('permission:lost-reasons.edit');

    // Followups routes
    Route::get('followups', [FollowupController::class, 'index'])
        ->middleware('permission:followups.view')
        ->name('followups.index');
    Route::get('followups/data', [FollowupController::class, 'listData'])
        ->middleware('permission:followups.view');
    Route::get('followups/today-reminders', [FollowupController::class, 'getTodayReminders']);
    Route::post('followups/store', [FollowupController::class, 'store'])
        ->middleware('permission:followups.create');
    Route::get('followups/edit/{id}', [FollowupController::class, 'edit'])
        ->middleware('permission:followups.edit');
    Route::post('followups/update/{id}', [FollowupController::class, 'update'])
        ->middleware('permission:followups.edit');
    Route::delete('followups/delete/{id}', [FollowupController::class, 'destroy'])
        ->middleware('permission:followups.delete');
    Route::post('followups/change-status/{id}', [FollowupController::class, 'changeStatus'])
        ->middleware('permission:followups.edit');
    Route::post('followups/reassign/{id}', [FollowupController::class, 'reassign']);
    Route::get('followups/reassignment-history', [FollowupController::class, 'reassignmentHistory']);
    Route::get('followups/leave-staff/{staffId}', [FollowupController::class, 'getLeaveStaffFollowups']);

    // Leads routes
    Route::get('leads', [LeadController::class, 'index'])
        ->middleware('permission:leads.view')
        ->name('leads.index');
    Route::get('leads/data', [LeadController::class, 'listData'])
        ->middleware('permission:leads.view');
    Route::post('leads/store', [LeadController::class, 'store'])
        ->middleware('permission:leads.create');
    Route::get('leads/edit/{id}', [LeadController::class, 'edit'])
        ->middleware('permission:leads.edit');
    Route::post('leads/update/{id}', [LeadController::class, 'update'])
        ->middleware('permission:leads.edit');
    Route::delete('leads/delete/{id}', [LeadController::class, 'destroy'])
        ->middleware('permission:leads.delete');
    Route::post('leads/change-status/{id}', [LeadController::class, 'changeStatus'])
        ->middleware('permission:leads.edit');

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

    // Lead Documents routes
    Route::get('lead-documents', [LeadDocumentController::class, 'index'])
        ->middleware('permission:lead-documents.view')
        ->name('lead-documents.index');
    Route::get('lead-documents/data', [LeadDocumentController::class, 'listData'])
        ->middleware('permission:lead-documents.view');
    Route::post('lead-documents/store', [LeadDocumentController::class, 'store'])
        ->middleware('permission:lead-documents.create');
    Route::get('lead-documents/edit/{id}', [LeadDocumentController::class, 'edit'])
        ->middleware('permission:lead-documents.edit');
    Route::post('lead-documents/update/{id}', [LeadDocumentController::class, 'update'])
        ->middleware('permission:lead-documents.edit');
    Route::delete('lead-documents/delete/{id}', [LeadDocumentController::class, 'destroy'])
        ->middleware('permission:lead-documents.delete');
    Route::get('lead-documents/download/{id}', [LeadDocumentController::class, 'download'])
        ->middleware('permission:lead-documents.view');

    // Templates routes
    Route::get('templates', [TemplateController::class, 'index'])
        ->middleware('permission:templates.view')
        ->name('templates.index');
    Route::get('templates/data', [TemplateController::class, 'listData'])
        ->middleware('permission:templates.view');
    Route::post('templates/store', [TemplateController::class, 'store'])
        ->middleware('permission:templates.create');
    Route::get('templates/edit/{id}', [TemplateController::class, 'edit'])
        ->middleware('permission:templates.edit');
    Route::post('templates/update/{id}', [TemplateController::class, 'update'])
        ->middleware('permission:templates.edit');
    Route::delete('templates/delete/{id}', [TemplateController::class, 'destroy'])
        ->middleware('permission:templates.delete');
    Route::post('templates/change-status/{id}', [TemplateController::class, 'changeStatus'])
        ->middleware('permission:templates.edit');

    // Call Recordings routes
    Route::get('call-recordings', [CallRecordingController::class, 'index'])
        ->middleware('permission:call-recordings.view')
        ->name('call-recordings.index');
    Route::get('call-recordings/data', [CallRecordingController::class, 'listData'])
        ->middleware('permission:call-recordings.view');
    Route::post('call-recordings/store', [CallRecordingController::class, 'store'])
        ->middleware('permission:call-recordings.create');
    Route::get('call-recordings/edit/{id}', [CallRecordingController::class, 'edit'])
        ->middleware('permission:call-recordings.edit');
    Route::post('call-recordings/update/{id}', [CallRecordingController::class, 'update'])
        ->middleware('permission:call-recordings.edit');
    Route::delete('call-recordings/delete/{id}', [CallRecordingController::class, 'destroy'])
        ->middleware('permission:call-recordings.delete');

    // Call Logs routes
    Route::get('call-logs', [CallLogController::class, 'index'])
        ->middleware('permission:call-logs.view')
        ->name('call-logs.index');
    Route::get('call-logs/data', [CallLogController::class, 'listData'])
        ->middleware('permission:call-logs.view');
    Route::post('call-logs/store', [CallLogController::class, 'store'])
        ->middleware('permission:call-logs.create');
    Route::get('call-logs/edit/{id}', [CallLogController::class, 'edit'])
        ->middleware('permission:call-logs.edit');
    Route::post('call-logs/update/{id}', [CallLogController::class, 'update'])
        ->middleware('permission:call-logs.edit');
    Route::delete('call-logs/delete/{id}', [CallLogController::class, 'destroy'])
        ->middleware('permission:call-logs.delete');

    // Call Log Report routes
    Route::get('call-logs/report', [CallLogController::class, 'report'])
        ->middleware('permission:call-log-reports.view')
        ->name('call-logs.report');
    Route::get('call-logs/report/data', [CallLogController::class, 'reportData'])
        ->middleware('permission:call-log-reports.view');

    // Attendance routes
    Route::get('attendance', [AttendanceController::class, 'index'])
        ->middleware('permission:attendance.view')
        ->name('attendance.index');
    Route::get('attendance/data', [AttendanceController::class, 'listData'])
        ->middleware('permission:attendance.view');
    Route::post('attendance/store', [AttendanceController::class, 'store'])
        ->middleware('permission:attendance.create');
    Route::get('attendance/edit/{id}', [AttendanceController::class, 'edit'])
        ->middleware('permission:attendance.edit');
    Route::post('attendance/update/{id}', [AttendanceController::class, 'update'])
        ->middleware('permission:attendance.edit');
    Route::delete('attendance/delete/{id}', [AttendanceController::class, 'destroy'])
        ->middleware('permission:attendance.delete');
    Route::post('attendance/mark-self', [AttendanceController::class, 'markSelfAttendance'])
        ->name('attendance.mark-self');

    // Attendance Report routes
    Route::get('attendance/report', [AttendanceController::class, 'report'])
        ->middleware('permission:attendance-reports.view')
        ->name('attendance.report');
    Route::get('attendance/report/data', [AttendanceController::class, 'reportData'])
        ->middleware('permission:attendance-reports.view');

    // Leave Management routes
    Route::get('leaves', [LeaveController::class, 'index'])
        ->middleware('permission:leaves.view')
        ->name('leaves.index');
    Route::get('leaves/data', [LeaveController::class, 'listData'])
        ->middleware('permission:leaves.view');
    Route::post('leaves/store', [LeaveController::class, 'store'])
        ->middleware('permission:leaves.create');
    Route::post('leaves/approve/{id}', [LeaveController::class, 'approve'])
        ->middleware('permission:leaves.approve');
    Route::post('leaves/reject/{id}', [LeaveController::class, 'reject'])
        ->middleware('permission:leaves.approve');
    Route::delete('leaves/delete/{id}', [LeaveController::class, 'destroy'])
        ->middleware('permission:leaves.delete');
    Route::get('leaves/salary-report', [LeaveController::class, 'salaryReportData'])
        ->middleware('permission:salary.view');
});
