<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StaffAuthController extends Controller
{

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'fcmtoken' => ['nullable', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($request->filled('fcmtoken')) {
            $user->fcmtoken = $request->input('fcmtoken');
            $user->save();
        }

        $token = $user->createToken('staff-api')->plainTextToken;

        $userData = $this->formatUserData($user);

        // Check today's pending follow-up reminders and trigger push notification on login
        $todayRemindersCount = 0;
        try {
            $today = \Carbon\Carbon::today()->toDateString();
            $userId = $user->id;
            $todayFollowups = \App\Models\Followup::with([
                'lead:lead_id,lead_title,customer_id,assigned_to',
                'lead.customer:customer_id,name,mobile,email',
            ])
            ->where('followup_status', 'Pending')
            ->whereDate('next_followup_date', '=', $today)
            ->where(function ($query) use ($userId) {
                $query->where('forward_to', $userId)
                      ->orWhere('created_by', $userId)
                      ->orWhereHas('lead', function ($q3) use ($userId) {
                          $q3->where('assigned_to', $userId);
                      })
                      ->orWhereHas('reassignments', function ($rq) use ($userId) {
                          $rq->where('new_staff_id', $userId);
                      });
            })
            ->get();

            $todayRemindersCount = $todayFollowups->count();

            if ($todayRemindersCount > 0 && !empty($user->fcmtoken)) {
                $firebaseService = app(\App\Services\FirebaseNotificationService::class);
                $firebaseService->sendFollowupReminderNotification($user, $todayFollowups);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error triggering reminders on login: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $token,
            'today_reminders_count' => $todayRemindersCount,
            'user' => $userData,
        ]);
    }

    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcmtoken' => ['required', 'string'],
        ]);

        $user = $request->user();
        $user->fcmtoken = $request->input('fcmtoken');
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'FCM token updated successfully.',
            'fcmtoken' => $user->fcmtoken,
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $userData = $this->formatUserData($user);
        $salaryStatus = (int) general_setting('salary', 0);

        return response()->json([
            'status'        => true,
            'salary_status' => $salaryStatus,
            'salary'        => $salaryStatus,
            'user'          => $userData,
        ]);
    }

    /**
     * Get staff menu permissions map matching Admin Panel sidebar.
     * GET /api/v1/staff/menu-access
     * GET /api/v1/staff/menus
     */
    public function menuAccess(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $menuAccess = $this->getMenuAccess($user);
        $menuList   = $this->getMenuList($user, $menuAccess);

        return response()->json([
            'status'      => true,
            'user_id'     => $user->id,
            'user_name'   => $user->name,
            'role'        => $user->getRoleNames()->first(),
            'menu_access' => $menuAccess,
            'menus'       => $menuList,
            'menu_names'  => collect($menuList)->where('has_access', true)->pluck('name')->values(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Check answered calls progress toward 50 calls check-out requirement.
     * GET /api/v1/staff/call-checkout-status
     */
    public function callCheckOutStatus(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $check = $user->canCheckOut();

        return response()->json([
            'status'          => true,
            'can_checkout'    => $check['allowed'],
            'can_logout'      => true,
            'message'         => $check['message'],
            'completed_calls' => $check['completed'],
            'required_calls'  => $check['required'],
            'remaining_calls' => $check['remaining'],
            'is_mandatory'    => $user->isSalesManager(),
        ]);
    }

    /**
     * Backward-compatible call-logout-status endpoint.
     * GET /api/v1/staff/call-logout-status
     */
    public function callLogoutStatus(Request $request)
    {
        return $this->callCheckOutStatus($request);
    }

    /**
     * Format user data with role, permissions, and menu_access map.
     */
    private function formatUserData(User $user): array
    {
        $roleName = $user->getRoleNames()->first();
        $userData = $user->toArray();
        unset($userData['roles']);
        $userData['role'] = $roleName;
        $userData['permissions'] = $user->getAllPermissions()->pluck('name')->values();
        $userData['menu_access'] = $this->getMenuAccess($user);
        $userData['menus'] = $this->getMenuList($user, $userData['menu_access']);
        $userData['menu_names'] = collect($userData['menus'])->where('has_access', true)->pluck('name')->values();

        $salaryStatus = (int) general_setting('salary', 0);
        $userData['salary_status'] = $salaryStatus;
        $userData['salary'] = $salaryStatus;

        $check = $user->canCheckOut();
        $userData['call_checkout_status'] = [
            'completed_calls' => $check['completed'],
            'required_calls'  => $check['required'],
            'remaining_calls' => $check['remaining'],
            'can_checkout'    => $check['allowed'],
            'is_mandatory'    => $user->isSalesManager(),
        ];
        $userData['call_logout_status'] = [
            'completed_calls' => $check['completed'],
            'required_calls'  => 0,
            'remaining_calls' => 0,
            'can_logout'      => true,
            'is_mandatory'    => false,
        ];

        return $userData;
    }

    /**
     * Build exact menu access permission map matching Admin Panel sidebar.
     */
    private function getMenuAccess(User $user): array
    {
        $isAdmin = $user->isAdmin() || $user->isSuperAdmin();
        $salaryEnabled = (int) general_setting('salary', 0) === 1;

        return [
            'dashboard'          => true,
            'customers'          => $isAdmin || $user->can('customers.view'),
            'coordinations'      => $isAdmin || $user->can('coordinations.view'),
            'demo_processes'     => $isAdmin || $user->can('demo-processes.view'),
            'leads'              => $isAdmin || $user->can('leads.view'),
            'sale_closed'        => $isAdmin || $user->can('sale-closed.view') || $user->can('leads.view') || $user->isStaff(),
            'followups'          => $isAdmin || $user->can('followups.view'),
            'lead_documents'     => $isAdmin || $user->can('lead-documents.view'),
            'call_recordings'    => $isAdmin || $user->can('call-recordings.view'),
            'call_logs'          => $isAdmin || $user->can('call-logs.view'),
            'call_reports'       => $isAdmin || $user->can('call-log-reports.view'),
            'lead_sources'       => $isAdmin || $user->can('lead-sources.view'),
            'lead_stages'        => $isAdmin || $user->can('lead-stages.view'),
            'lead_requirements'  => $isAdmin || $user->can('lead-requirements.view'),
            'lost_reasons'       => $isAdmin || $user->can('lost-reasons.view'),
            'credit_requests'    => $isAdmin || $user->can('credit-requests.view') || $user->can('credit-requests.create') || $user->isStaff(),
            'payments'           => $isAdmin || $user->can('payments.view'),
            'attendance'         => $isAdmin || $user->can('attendance.view'),
            'attendance_reports' => $isAdmin || $user->can('attendance-reports.view'),
            'leaves'             => $isAdmin || $user->can('leaves.view') || $user->can('leaves.create') || $user->isStaff(),
            'salary'             => $salaryEnabled && ($isAdmin || $user->can('salary.view')),
            'incentives'         => $isAdmin || $user->can('incentives.view'),
            'branches'           => $isAdmin || $user->can('branches.view') || $user->isStaff(),
            'teams'              => $isAdmin || $user->can('teams.view') || $user->isStaff(),
            'staff'              => $isAdmin || $user->can('staff.view'),
            'roles'              => $isAdmin || $user->can('roles.view'),
            'templates'          => $isAdmin || $user->can('templates.view'),
            'proposals'          => $isAdmin || $user->can('proposals.view'),
            'pending_works'      => $isAdmin || $user->can('pending-works.view') || true, // Accessible to staff
            'daily_learnings'    => $isAdmin || $user->can('daily-learnings.view') || true,
            'commitments'        => $isAdmin || $user->can('commitments.view') || true,
            'settings'           => $isAdmin || $user->can('general-settings.view') || $user->can('lead-settings.view') || $user->can('customer-settings.view') || $user->can('followup-settings.view') || $user->can('credit-request-settings.view') || $user->can('demo-process-settings.view'),
            'audit_logs'         => $isAdmin || $user->can('audit-logs.view'),
            'notifications'      => true,

            // Aliases (kebab-case, singular, etc.) for compatibility with different API clients
            'sale-closed'        => $isAdmin || $user->can('sale-closed.view') || $user->can('leads.view') || $user->isStaff(),
            'sales_closed'       => $isAdmin || $user->can('sale-closed.view') || $user->can('leads.view') || $user->isStaff(),
            'credit-requests'    => $isAdmin || $user->can('credit-requests.view') || $user->can('credit-requests.create') || $user->isStaff(),
            'lead-documents'     => $isAdmin || $user->can('lead-documents.view'),
            'call-recordings'    => $isAdmin || $user->can('call-recordings.view'),
            'call-logs'          => $isAdmin || $user->can('call-logs.view'),
            'call-reports'       => $isAdmin || $user->can('call-log-reports.view'),
            'call_log_reports'   => $isAdmin || $user->can('call-log-reports.view'),
            'lead-sources'       => $isAdmin || $user->can('lead-sources.view'),
            'lead-stages'        => $isAdmin || $user->can('lead-stages.view'),
            'lead-requirements'  => $isAdmin || $user->can('lead-requirements.view'),
            'lost-reasons'       => $isAdmin || $user->can('lost-reasons.view'),
            'attendance-reports' => $isAdmin || $user->can('attendance-reports.view'),
            'leave_requests'     => $isAdmin || $user->can('leaves.view') || $user->can('leaves.create') || $user->isStaff(),
            'leave-requests'     => $isAdmin || $user->can('leaves.view') || $user->can('leaves.create') || $user->isStaff(),
            'pending-works'      => $isAdmin || $user->can('pending-works.view') || true,
            'pending_work'       => $isAdmin || $user->can('pending-works.view') || true,
            'daily-learnings'    => $isAdmin || $user->can('daily-learnings.view') || true,
            'daily_learning'     => $isAdmin || $user->can('daily-learnings.view') || true,
            'commitment'         => $isAdmin || $user->can('commitments.view') || true,
            'coordination'       => $isAdmin || $user->can('coordinations.view'),
            'demo-processes'     => $isAdmin || $user->can('demo-processes.view'),
            'demo_process'       => $isAdmin || $user->can('demo-processes.view'),
            'audit-logs'         => $isAdmin || $user->can('audit-logs.view'),
        ];
    }

    /**
     * Get structured menu list matching Admin Panel sidebar.
     */
    private function getMenuList(User $user, array $menuAccess): array
    {
        return [
            [
                'key'        => 'dashboard',
                'name'       => 'Dashboards',
                'title'      => 'Dashboards',
                'icon'       => 'bx bx-home-smile',
                'has_access' => (bool) ($menuAccess['dashboard'] ?? false),
            ],
            [
                'key'        => 'branches',
                'name'       => 'Branches',
                'title'      => 'Branches',
                'icon'       => 'bx bx-buildings',
                'has_access' => (bool) ($menuAccess['branches'] ?? false),
            ],
            [
                'key'        => 'teams',
                'name'       => 'Teams',
                'title'      => 'Teams',
                'icon'       => 'bx bx-group',
                'has_access' => (bool) ($menuAccess['teams'] ?? false),
            ],
            [
                'key'        => 'roles',
                'name'       => 'Role Master',
                'title'      => 'Role Master',
                'icon'       => 'bx bx-lock-alt',
                'has_access' => (bool) ($menuAccess['roles'] ?? false),
            ],
            [
                'key'        => 'staff',
                'name'       => 'Staff',
                'title'      => 'Staff',
                'icon'       => 'bx bx-user-check',
                'has_access' => (bool) ($menuAccess['staff'] ?? false),
            ],
            [
                'key'        => 'attendance',
                'name'       => 'Attendance',
                'title'      => 'Attendance',
                'icon'       => 'bx bx-time-five',
                'has_access' => (bool) ($menuAccess['attendance'] ?? false),
            ],
            [
                'key'        => 'attendance_reports',
                'name'       => 'Attendance Report',
                'title'      => 'Attendance Report',
                'icon'       => 'bx bx-calendar-check',
                'has_access' => (bool) ($menuAccess['attendance_reports'] ?? false),
            ],
            [
                'key'        => 'leaves',
                'name'       => 'Leave & Salary',
                'title'      => 'Leave & Salary',
                'icon'       => 'bx bx-calendar-x',
                'has_access' => (bool) ($menuAccess['leaves'] ?? false),
            ],
            [
                'key'        => 'salary',
                'name'       => 'Salary',
                'title'      => 'Salary',
                'icon'       => 'bx bx-wallet',
                'has_access' => (bool) ($menuAccess['salary'] ?? false),
            ],
            [
                'key'        => 'incentives',
                'name'       => 'Incentives',
                'title'      => 'Incentives',
                'icon'       => 'bx bx-dollar-circle',
                'has_access' => (bool) ($menuAccess['incentives'] ?? false),
            ],
            [
                'key'        => 'customers',
                'name'       => 'Customers',
                'title'      => 'Customers',
                'icon'       => 'bx bx-user',
                'has_access' => (bool) ($menuAccess['customers'] ?? false),
            ],
            [
                'key'        => 'coordinations',
                'name'       => 'Coordination',
                'title'      => 'Coordination',
                'icon'       => 'bx bx-git-repo-forked',
                'has_access' => (bool) ($menuAccess['coordinations'] ?? false),
            ],
            [
                'key'        => 'demo_processes',
                'name'       => 'Demo Process',
                'title'      => 'Demo Process',
                'icon'       => 'bx bx-slideshow',
                'has_access' => (bool) ($menuAccess['demo_processes'] ?? false),
            ],
            [
                'key'        => 'leads',
                'name'       => 'Leads',
                'title'      => 'Leads',
                'icon'       => 'bx bx-target-lock',
                'has_access' => (bool) ($menuAccess['leads'] ?? false),
            ],
            [
                'key'        => 'sale_closed',
                'name'       => 'Sale Closed',
                'title'      => 'Sale Closed',
                'icon'       => 'bx bx-check-double',
                'has_access' => (bool) ($menuAccess['sale_closed'] ?? false),
            ],
            [
                'key'        => 'followups',
                'name'       => 'Follow-ups',
                'title'      => 'Follow-ups',
                'icon'       => 'bx bx-calendar-event',
                'has_access' => (bool) ($menuAccess['followups'] ?? false),
            ],
            [
                'key'        => 'lead_documents',
                'name'       => 'Lead Documents',
                'title'      => 'Lead Documents',
                'icon'       => 'bx bx-file',
                'has_access' => (bool) ($menuAccess['lead_documents'] ?? false),
            ],
            [
                'key'        => 'call_recordings',
                'name'       => 'Call Recordings',
                'title'      => 'Call Recordings',
                'icon'       => 'bx bx-microphone',
                'has_access' => (bool) ($menuAccess['call_recordings'] ?? false),
            ],
            [
                'key'        => 'call_logs',
                'name'       => 'Call Logs',
                'title'      => 'Call Logs',
                'icon'       => 'bx bx-phone-call',
                'has_access' => (bool) ($menuAccess['call_logs'] ?? false),
            ],
            [
                'key'        => 'call_reports',
                'name'       => 'Call Log Report',
                'title'      => 'Call Log Report',
                'icon'       => 'bx bx-line-chart',
                'has_access' => (bool) ($menuAccess['call_reports'] ?? false),
            ],
            [
                'key'        => 'lead_sources',
                'name'       => 'Lead Sources',
                'title'      => 'Lead Sources',
                'icon'       => 'bx bx-git-branch',
                'has_access' => (bool) ($menuAccess['lead_sources'] ?? false),
            ],
            [
                'key'        => 'lead_stages',
                'name'       => 'Lead Stages',
                'title'      => 'Lead Stages',
                'icon'       => 'bx bx-bar-chart-alt-2',
                'has_access' => (bool) ($menuAccess['lead_stages'] ?? false),
            ],
            [
                'key'        => 'lead_requirements',
                'name'       => 'Lead Requirements',
                'title'      => 'Lead Requirements',
                'icon'       => 'bx bx-list-check',
                'has_access' => (bool) ($menuAccess['lead_requirements'] ?? false),
            ],
            [
                'key'        => 'lost_reasons',
                'name'       => 'Lost Reasons',
                'title'      => 'Lost Reasons',
                'icon'       => 'bx bx-dislike',
                'has_access' => (bool) ($menuAccess['lost_reasons'] ?? false),
            ],
            [
                'key'        => 'credit_requests',
                'name'       => 'Credit Requests',
                'title'      => 'Credit Requests',
                'icon'       => 'bx bx-credit-card',
                'has_access' => (bool) ($menuAccess['credit_requests'] ?? false),
            ],
            [
                'key'        => 'payments',
                'name'       => 'Payments',
                'title'      => 'Payments',
                'icon'       => 'bx bx-receipt',
                'has_access' => (bool) ($menuAccess['payments'] ?? false),
            ],
            [
                'key'        => 'proposals',
                'name'       => 'Proposals',
                'title'      => 'Proposals',
                'icon'       => 'bx bx-file-blank',
                'has_access' => (bool) ($menuAccess['proposals'] ?? false),
            ],
            [
                'key'        => 'templates',
                'name'       => 'Templates',
                'title'      => 'Templates',
                'icon'       => 'bx bx-copy-alt',
                'has_access' => (bool) ($menuAccess['templates'] ?? false),
            ],
            [
                'key'        => 'pending_works',
                'name'       => 'Pending Work',
                'title'      => 'Pending Work',
                'icon'       => 'bx bx-task',
                'has_access' => (bool) ($menuAccess['pending_works'] ?? false),
            ],
            [
                'key'        => 'daily_learnings',
                'name'       => 'Daily Learning',
                'title'      => 'Daily Learning',
                'icon'       => 'bx bx-book-open',
                'has_access' => (bool) ($menuAccess['daily_learnings'] ?? false),
            ],
            [
                'key'        => 'commitments',
                'name'       => 'Commitment',
                'title'      => 'Commitment',
                'icon'       => 'bx bx-check-shield',
                'has_access' => (bool) ($menuAccess['commitments'] ?? false),
            ],
            [
                'key'        => 'settings',
                'name'       => 'Settings',
                'title'      => 'Settings',
                'icon'       => 'bx bx-cog',
                'has_access' => (bool) ($menuAccess['settings'] ?? false),
            ],
            [
                'key'        => 'audit_logs',
                'name'       => 'Audit Logs',
                'title'      => 'Audit Logs',
                'icon'       => 'bx bx-history',
                'has_access' => (bool) ($menuAccess['audit_logs'] ?? false),
            ],
            [
                'key'        => 'notifications',
                'name'       => 'Notifications',
                'title'      => 'Notifications',
                'icon'       => 'bx bx-bell',
                'has_access' => (bool) ($menuAccess['notifications'] ?? false),
            ],
        ];
    }
}

