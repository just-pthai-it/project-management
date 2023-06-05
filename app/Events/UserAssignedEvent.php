<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAssignedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Model $object;
    public User $causer;
    public array $userIds;

    /**
     * @param Model $object
     * @param User  $causer
     * @param array $userIds
     */
    public function __construct (Model $object, User $causer, array $userIds)
    {
        $this->object  = $object;
        $this->causer  = $causer;
        $this->userIds = $userIds;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn ()
    {
        return new PrivateChannel('channel-name');
    }
}
