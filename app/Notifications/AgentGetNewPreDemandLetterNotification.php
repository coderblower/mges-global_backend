<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AgentGetNewPreDemandLetterNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Use 'database' instead of 'mail'
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        // Store relevant data in the notification record
        return [
            'message' => 'You have got new pre demand letters. have a look!!',
            'action_url' => url('/demand-letters'),  // Example action URL
            'created_at' => now(),
        ];
    }
}
