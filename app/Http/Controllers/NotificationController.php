<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get recent notifications for the logged in user.
     * Super Admin sees all staff notifications; regular staff users see only their own notifications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if ($user->isSuperAdmin()) {
            $notifications = DatabaseNotification::latest()->take(20)->get();
            $unreadCount   = DatabaseNotification::whereNull('read_at')->count();
        } else {
            $notifications = $user->notifications()
                ->where('notifiable_type', $user->getMorphClass())
                ->where('notifiable_id', $user->id)
                ->take(20)
                ->get();

            $unreadCount = $user->unreadNotifications()
                ->where('notifiable_type', $user->getMorphClass())
                ->where('notifiable_id', $user->id)
                ->count();
        }

        $formatted = $notifications->map(function ($n) use ($user) {
            $data = $n->data;
            $title = $data['title'] ?? 'Notification';
            $message = $data['message'] ?? '';

            if ($user->isSuperAdmin()) {
                $staff = \App\Models\User::find($n->notifiable_id);
                if ($staff) {
                    $title = "{$title} ({$staff->name})";
                    $cleanMsg = str_replace(['Your ', 'your '], "{$staff->name}'s ", $message);
                    $message = "{$staff->name} ({$staff->email}): {$cleanMsg}";
                }
            }

            return [
                'id'         => $n->id,
                'title'      => $title,
                'message'    => $message,
                'read_at'    => $n->read_at ? $n->read_at->toIso8601String() : null,
                'is_read'    => !is_null($n->read_at),
                'created_at' => $n->created_at ? $n->created_at->diffForHumans() : '',
            ];
        });

        return response()->json([
            'status'       => true,
            'unread_count' => $unreadCount,
            'data'         => $formatted,
        ]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if ($user->isSuperAdmin()) {
            $notification = DatabaseNotification::where('id', $id)->first();
        } else {
            $notification = $user->notifications()
                ->where('notifiable_type', $user->getMorphClass())
                ->where('notifiable_id', $user->id)
                ->where('id', $id)
                ->first();
        }

        if ($notification) {
            $notification->markAsRead();
        }

        if ($user->isSuperAdmin()) {
            $unreadCount = DatabaseNotification::whereNull('read_at')->count();
        } else {
            $unreadCount = $user->unreadNotifications()
                ->where('notifiable_type', $user->getMorphClass())
                ->where('notifiable_id', $user->id)
                ->count();
        }

        return response()->json([
            'status'       => true,
            'unread_count' => $unreadCount,
            'message'      => 'Notification marked as read.'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if ($user->isSuperAdmin()) {
            DatabaseNotification::whereNull('read_at')->get()->each(function ($n) {
                $n->markAsRead();
            });
        } else {
            $user->unreadNotifications()
                ->where('notifiable_type', $user->getMorphClass())
                ->where('notifiable_id', $user->id)
                ->get()
                ->each(function ($n) {
                    $n->markAsRead();
                });
        }

        return response()->json([
            'status'       => true,
            'unread_count' => 0,
            'message'      => 'All notifications marked as read.'
        ]);
    }
}
