<?php

namespace App\Notifications;

use App\Models\DemoProcess;
use Illuminate\Notifications\Notification;

class DemoProcessCreated extends Notification
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
        $products = $this->demoProcess->leadSource 
            ? $this->demoProcess->leadSource->name 
            : (is_array($this->demoProcess->product_names) ? implode(', ', $this->demoProcess->product_names) : ($this->demoProcess->product_names ?? 'N/A'));
        $creatorName = $this->demoProcess->creator ? $this->demoProcess->creator->name : 'Sales Staff';
        $demoDate = $this->demoProcess->demo_date ? $this->demoProcess->demo_date->format('d/m/Y') : 'N/A';
        $demoTime = $this->demoProcess->demo_time ?? 'N/A';

        $msg = "New Demo Process created for {$this->demoProcess->customer_name} ({$this->demoProcess->customer_phone}). Product: {$products}. Date: {$demoDate}, Timing: {$demoTime}. Created By: {$creatorName}.";

        return [
            'title'           => 'Demo Process Created',
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
