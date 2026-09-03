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
        return [
            'title'             => 'Credit Request Approved by Product Manager',
            'message'           => 'Product Manager has approved the Credit Request. The approval process is completed.',
            'credit_request_id' => $this->creditRequest->credit_request_id ?? $this->creditRequest->id,
            'status'            => $this->creditRequest->status,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
