<?php

namespace App\Notifications;

use App\Models\CreditRequest;
use Illuminate\Notifications\Notification;

class CreditRequestCreatedNotification extends Notification
{
    public function __construct(
        public CreditRequest $creditRequest
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $amount = number_format($this->creditRequest->credit_amount, 2);
        $customerName = $this->creditRequest->username ?? ($this->creditRequest->customer->name ?? 'Customer');

        if ($notifiable->id == $this->creditRequest->requested_by) {
            $title = 'Credit Request Submitted';
            $message = "Your credit request of ₹{$amount} for {$customerName} has been submitted (Pending Admin Approval).";
        } else {
            $requesterName = $this->creditRequest->requester ? $this->creditRequest->requester->name : 'Sales Staff';
            $title = 'New Credit Request Pending Approval';
            $message = "{$requesterName} submitted a credit request of ₹{$amount} for {$customerName} (Pending Admin Approval).";
        }

        return [
            'title'             => $title,
            'message'           => $message,
            'credit_request_id' => $this->creditRequest->credit_request_id ?? $this->creditRequest->id,
            'status'            => $this->creditRequest->status,
            'url'               => url('/admin/credit-requests'),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
