<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Notification $notification;
    private array $userIds;

    /**
     * @param Notification $notification
     * @param array        $userIds
     */
    public function __construct (Notification $notification, array $userIds)
    {
        $this->notification = $notification;
        $this->userIds      = $userIds;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn() : Channel|array
    {
        return $this->userIds;
    }

    public function broadcastAs ()
    {
        return 'notification-created';
    }
}
