<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ObjectResourceUpdatedEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Model $object;
    public User $causer;
    public string $action;
    public string $resource;
    public string $notificationContent;

    /**
     * @param Model  $object
     * @param User   $causer
     * @param string $action
     */
    public function __construct (Model $object, User $causer, string $action)
    {
        $this->object = $object;
        $this->causer = $causer;
        $this->action = $action;
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
