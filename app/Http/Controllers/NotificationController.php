<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get recent notifications for the logged in user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $notifications = $user->notifications()->take(20)->get();
        $unreadCount   = $user->unreadNotifications()->count();

        $formatted = $notifications->map(function ($n) {
            $data = $n->data;
            return [
                'id'         => $n->id,
                'title'      => $data['title'] ?? 'Notification',
                'message'    => $data['message'] ?? '',
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

        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json([
            'status'       => true,
            'unread_count' => $user->unreadNotifications()->count(),
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

        $user->unreadNotifications->markAsRead();

        return response()->json([
            'status'       => true,
            'unread_count' => 0,
            'message'      => 'All notifications marked as read.'
        ]);
    }
}
