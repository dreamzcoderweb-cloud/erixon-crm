<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

class AdminLeaveRequestReceived extends Notification
{
    public function __construct(
        public LeaveRequest $leaveRequest,
        public User $staffUser
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $staffName  = $this->staffUser->name ?? 'Staff';
        $staffEmail = $this->staffUser->email ?? '';
        $fromDate   = Carbon::parse($this->leaveRequest->from_date)->format('d-m-Y');
        $toDate     = Carbon::parse($this->leaveRequest->to_date)->format('d-m-Y');
        $days       = $this->leaveRequest->number_of_days;

        return [
            'title'    => 'New Staff Leave Request Pending Approval',
            'message'  => "{$staffName} ({$staffEmail}) submitted a leave request for {$fromDate} to {$toDate} ({$days} day(s)) pending approval.",
            'leave_id' => $this->leaveRequest->id,
            'user_id'  => $this->staffUser->id,
            'status'   => 'Pending',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
