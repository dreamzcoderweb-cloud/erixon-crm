<?php

namespace App\Notifications;

use App\Models\DemoProcess;
use Illuminate\Notifications\Notification;

class DemoProcessConflictNotification extends Notification
{
    public function __construct(
        public string $demoDate,
        public string $demoTime,
        public ?DemoProcess $conflictingDemo = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'           => 'Demo Slot Already Booked',
            'message'         => 'A demo is already scheduled for this time. Please schedule the demo at least 1 hour later.',
            'demo_date'       => $this->demoDate,
            'demo_time'       => $this->demoTime,
            'conflict_id'     => $this->conflictingDemo?->demo_process_id,
            'type'            => 'demo_process_conflict',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
