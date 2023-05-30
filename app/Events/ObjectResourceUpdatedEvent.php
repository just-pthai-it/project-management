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
    public User $user;
    public string $action;
    public string $resource;
    public string $preposition;
    public string $notificationContent;

    /**
     * @param Model  $object
     * @param User   $user
     * @param string $action
     * @param string $resource
     * @param string $preposition
     * @param string $notificationContent
     */
    public function __construct (Model $object, User $user, string $action, string $resource, string $preposition = 'to', string $notificationContent = '')
    {
        $this->object      = $object;
        $this->user        = $user;
        $this->action      = $action;
        $this->resource    = $resource;
        $this->preposition = $preposition;
        $this->notificationContent = $notificationContent;
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
