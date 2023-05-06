<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Model $object;
    public array $userIds;

    /**
     * @param Model $notifiable
     * @param array $userIds
     */
    public function __construct (Model $notifiable, array $userIds)
    {
        $this->object  = $notifiable;
        $this->userIds = $userIds;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
