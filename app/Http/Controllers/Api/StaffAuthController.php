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

        return response()->json([
            'status' => true,
            'user'   => $userData,
        ]);
    }

    /**
     * Get staff menu permissions map matching Admin Panel sidebar.
     * GET /api/v1/staff/menu-access
     */
    public function menuAccess(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        return response()->json([
            'status'      => true,
            'user_id'     => $user->id,
            'user_name'   => $user->name,
            'role'        => $user->getRoleNames()->first(),
            'menu_access' => $this->getMenuAccess($user),
            'permissions' => $user->getAllPermissions()->pluck('name')->values(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
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

        return $userData;
    }

    /**
     * Build exact menu access permission map matching Admin Panel sidebar.
     */
    private function getMenuAccess(User $user): array
    {
        $isAdmin = $user->isAdmin() || $user->isSuperAdmin();

        return [
            'dashboard'          => true,
            'customers'          => $isAdmin || $user->can('customers.view'),
            'coordinations'      => $isAdmin || $user->can('coordinations.view'),
            'demo_processes'     => $isAdmin || $user->can('demo-processes.view'),
            'leads'              => $isAdmin || $user->can('leads.view'),
            'followups'          => $isAdmin || $user->can('followups.view'),
            'lead_documents'     => $isAdmin || $user->can('lead-documents.view'),
            'call_recordings'    => $isAdmin || $user->can('call-recordings.view'),
            'call_logs'          => $isAdmin || $user->can('call-logs.view'),
            'call_reports'       => $isAdmin || $user->can('call-log-reports.view'),
            'attendance'         => $isAdmin || $user->can('attendance.view'),
            'attendance_reports' => $isAdmin || $user->can('attendance-reports.view'),
            'leaves'             => $isAdmin || $user->can('leaves.view'),
            'incentives'         => $isAdmin || $user->can('incentives.view'),
            'staff'              => $isAdmin || $user->can('staff.view'),
            'roles'              => $isAdmin || $user->can('roles.view'),
            'templates'          => $isAdmin || $user->can('templates.view'),
            'settings'           => $isAdmin || $user->can('general-settings.view') || $user->can('lead-settings.view'),
        ];
    }
}

