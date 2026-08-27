<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class LeaveQuotaCompleted extends Notification
{
    public function __construct(
        public string $monthName,
        public float $usedLeaveDays,
        public float $allowedLeaveDays,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Leave quota completed',
            'message' => "Your allowed leave quota for {$this->monthName} has been completed.",
            'month' => $this->monthName,
            'used_leave_days' => $this->usedLeaveDays,
            'allowed_leave_days' => $this->allowedLeaveDays,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Leave quota completed',
            'message' => "Your allowed leave quota for {$this->monthName} has been completed.",
        ];
    }
}
