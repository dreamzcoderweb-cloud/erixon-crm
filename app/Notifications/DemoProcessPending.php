<?php

namespace App\Notifications;

use App\Models\DemoProcess;
use Illuminate\Notifications\Notification;

class DemoProcessPending extends Notification
{
    public function __construct(
        public DemoProcess $demoProcess
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $demoDate = $this->demoProcess->demo_date ? $this->demoProcess->demo_date->format('d/m/Y') : 'N/A';
        $demoTime = $this->demoProcess->demo_time ?? 'N/A';

        $msg = "Demo for {$this->demoProcess->customer_name} is pending. Demo Date: {$demoDate}, Timing: {$demoTime}.";

        return [
            'title'           => 'Demo Process Pending',
            'message'         => $msg,
            'demo_process_id' => $this->demoProcess->demo_process_id,
            'customer_name'   => $this->demoProcess->customer_name,
            'status'          => $this->demoProcess->status,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
