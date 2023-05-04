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

class ObjectUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Model $object;
    public User $user;
    public array $oldData;

    /**
     * @param Model $object
     * @param User  $user
     * @param array $oldData
     */
    public function __construct (Model $object, User $user, array $oldData)
    {
        $this->object  = $object;
        $this->user    = $user;
        $this->oldData = $oldData;
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
