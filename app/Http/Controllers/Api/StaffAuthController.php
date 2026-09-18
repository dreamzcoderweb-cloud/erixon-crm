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

        $roleName = $user->getRoleNames()->first();
        $userData = $user->toArray();
        unset($userData['roles']);
        $userData['role'] = $roleName;

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
        $roleName = $user->getRoleNames()->first();
        $userData = $user->toArray();
        unset($userData['roles']);
        $userData['role'] = $roleName;

        return response()->json([
            'user' => $userData,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}

