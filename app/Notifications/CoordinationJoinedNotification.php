<?php

namespace App\Notifications;

use App\Models\Coordination;
use App\Models\User;
use Illuminate\Notifications\Notification;

class CoordinationJoinedNotification extends Notification
{
    public function __construct(
        public Coordination $coordination,
        public User $joinedUser
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        if ($notifiable->id == $this->joinedUser->id) {
            $title = 'Coordination Joined';
            $message = "You have successfully joined coordination: \"{$this->coordination->title}\".";
        } else {
            $title = 'Staff Joined Coordination';
            $message = "{$this->joinedUser->name} has joined your coordination: \"{$this->coordination->title}\".";
        }

        return [
            'title'           => $title,
            'message'         => $message,
            'coordination_id' => $this->coordination->coordination_id,
            'url'             => url('/admin/coordinations'),
            'type'            => 'coordination_joined',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
