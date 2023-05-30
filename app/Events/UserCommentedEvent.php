<?php

namespace App\Events;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCommentedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public Model $object;
    public Comment $comment;
    public ?Comment $previousComment;

    /**
     * @param Model        $object
     * @param Comment      $comment
     * @param Comment|null $previousComment
     */
    public function __construct (Model $object, Comment $comment, ?Comment $previousComment)
    {
        $this->object          = $object;
        $this->comment         = $comment;
        $this->previousComment = $previousComment;
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
