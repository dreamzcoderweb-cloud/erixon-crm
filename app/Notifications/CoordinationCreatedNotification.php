<?php

namespace App\Notifications;

use App\Models\Coordination;
use Illuminate\Notifications\Notification;

class CoordinationCreatedNotification extends Notification
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
        return [
            'title'           => 'Coordination Created',
            'message'         => "You have created coordination: \"{$this->coordination->title}\".",
            'coordination_id' => $this->coordination->coordination_id,
            'url'             => url('/admin/coordinations'),
            'type'            => 'coordination_created',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
