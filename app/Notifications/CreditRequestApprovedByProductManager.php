<?php

namespace App\Notifications;

use App\Models\CreditRequest;
use Illuminate\Notifications\Notification;

class CreditRequestApprovedByProductManager extends Notification
{
    public function __construct(
        public CreditRequest $creditRequest
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $amount = number_format($this->creditRequest->credit_amount, 2);
        $customerName = $this->creditRequest->username ?? ($this->creditRequest->customer->name ?? 'Customer');

        if ($notifiable->id == $this->creditRequest->requested_by) {
            $title = 'Credit Request Completed';
            $message = "Your credit request of ₹{$amount} for {$customerName} has been approved by Product Manager and added to customer credit balance.";
        } else {
            $title = 'Credit Request Approved by Product Manager';
            $message = "Product Manager has approved the credit request of ₹{$amount} for {$customerName}. Credit has been added to customer balance.";
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
