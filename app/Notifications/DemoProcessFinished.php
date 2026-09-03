<?php

namespace App\Notifications;

use App\Models\DemoProcess;
use Illuminate\Notifications\Notification;

class DemoProcessFinished extends Notification
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
        $msg = "Demo for {$this->demoProcess->customer_name} has been completed successfully.";

        return [
            'title'           => 'Demo Process Finished',
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
