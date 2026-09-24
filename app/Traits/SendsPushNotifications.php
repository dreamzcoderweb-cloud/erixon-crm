<?php

namespace App\Traits;

use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;

trait SendsPushNotifications
{
    /**
     * Send FCM Push Notification safely if user has registered fcmtoken.
     *
     * @param User $user
     * @param string $title
     * @param string $message
     * @param array $data
     * @return void
     */
    protected function sendPushNotification(User $user, string $title, string $message, array $data = []): void
    {
        if (!empty($user->fcmtoken)) {
            try {
                $fcm = app(FirebaseNotificationService::class);
                $fcm->sendNotification($user->fcmtoken, $title, $message, $data);
            } catch (\Throwable $e) {
                Log::warning(static::class . ': FCM push delivery error: ' . $e->getMessage());
            }
        }
    }
}
