<?php

namespace App\Events;

use App\Models\ActivityLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ActivityLogCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ActivityLog $activityLog;


    /**
     * @param ActivityLog $activityLog
     */
    public function __construct (ActivityLog $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn () : Channel|array
    {
        return new Channel(Str::lower(class_basename(get_class($this->activityLog->objectable))) . "_{$this->activityLog->objectable->id}");
    }
}
