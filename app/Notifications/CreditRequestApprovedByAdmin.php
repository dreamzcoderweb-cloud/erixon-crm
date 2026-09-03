<?php

namespace App\Notifications;

use App\Models\CreditRequest;
use Illuminate\Notifications\Notification;

class CreditRequestApprovedByAdmin extends Notification
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
        return [
            'title'             => 'Credit Request Approved',
            'message'           => 'Super Admin has approved the Credit Request. Next, Product Manager approval is required.',
            'credit_request_id' => $this->creditRequest->credit_request_id ?? $this->creditRequest->id,
            'status'            => $this->creditRequest->status,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
