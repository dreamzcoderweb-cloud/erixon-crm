<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

class LeaveRequestSubmitted extends Notification
{
    public function __construct(
        public LeaveRequest $leaveRequest
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $fromDate  = Carbon::parse($this->leaveRequest->from_date)->format('d-m-Y');
        $toDate    = Carbon::parse($this->leaveRequest->to_date)->format('d-m-Y');
        $monthName = Carbon::parse($this->leaveRequest->from_date)->format('F Y');
        $days      = $this->leaveRequest->number_of_days;

        return [
            'title'    => 'Leave Request Pending Approval',
            'message'  => "Your leave request for {$fromDate} to {$toDate} ({$days} day(s)) [{$monthName}] has been submitted and is pending approval.",
            'month'    => $monthName,
            'leave_id' => $this->leaveRequest->id,
            'status'   => 'Pending',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
