<?php

namespace App\Notifications;

use App\Models\Coordination;
use Illuminate\Notifications\Notification;

class CoordinationAssignedNotification extends Notification
{
    public function __construct(
        public Coordination $coordination
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $creatorName = $this->coordination->creator ? $this->coordination->creator->name : 'Staff';

        return [
            'title'           => 'New Coordination Assigned',
            'message'         => "You have been added to coordination: \"{$this->coordination->title}\" by {$creatorName}.",
            'coordination_id' => $this->coordination->coordination_id,
            'url'             => url('/admin/coordinations'),
            'type'            => 'coordination_assigned',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
