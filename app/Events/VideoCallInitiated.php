<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;



class VideoCallInitiated implements ShouldBroadcast
{
    use InteractsWithSockets;

    public $recipientId;

    public function __construct($recipientId)
    {
        $this->recipientId = $recipientId;
    }

    /**
     * The channel on which the event should be broadcast.
     *
     * @return array|string
     */
    public function broadcastOn()
    {
        return ['video-call-channel'];
    }

    /**
     * The event name to be broadcasted.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'video-call-initiated';
    }
}
