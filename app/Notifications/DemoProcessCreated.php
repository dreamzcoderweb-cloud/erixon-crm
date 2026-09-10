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
        $products = 'N/A';
        if ($this->demoProcess->leadRequirement) {
            $products = $this->demoProcess->leadRequirement->name;
        } elseif ($this->demoProcess->lead_requirement_id) {
            $req = \App\Models\LeadRequirement::find($this->demoProcess->lead_requirement_id);
            if ($req) {
                $products = $req->name;
            }
        } elseif ($this->demoProcess->customer_phone) {
            $lead = \App\Models\Lead::with('leadRequirement')
                ->whereHas('customer', function ($q) {
                    $q->where('mobile', $this->demoProcess->customer_phone);
                })
                ->latest('lead_id')
                ->first();
            if ($lead && $lead->leadRequirement) {
                $products = $lead->leadRequirement->name;
            }
        }
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
