<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Notifications\Notification;

class LeaveApprovalStageNotification extends Notification
{
    public function __construct(
        public LeaveRequest $leaveRequest,
        public string $title,
        public string $message,
        public string $stage = 'tl',
        public ?User $actor = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'          => $this->title,
            'message'        => $this->message,
            'leave_id'       => $this->leaveRequest->id,
            'stage'          => $this->stage,
            'status'         => $this->leaveRequest->status,
            'user_id'        => $this->leaveRequest->user_id,
            'applicant_name' => $this->leaveRequest->user?->name,
            'type'           => 'leave_approval',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
