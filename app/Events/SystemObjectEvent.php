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

class SystemObjectEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Model $object;
    public User $causer;
    public string $action;
    public array $oldData;

    /**
     * @param Model  $object
     * @param User   $causer
     * @param string $action
     * @param array  $oldData
     */
    public function __construct (Model $object, User $causer, string $action, array $oldData = [])
    {
        $this->object  = $object;
        $this->causer  = $causer;
        $this->action  = $action;
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
