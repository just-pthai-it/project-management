<?php

namespace App\Events;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UsersAssignedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Project|Task $object;
    public User $causer;
    public array $userIds;

    /**
     * @param Project|Task $object
     * @param User         $causer
     * @param array        $userIds
     */
    public function __construct (Project|Task $object, User $causer, array $userIds)
    {
        $this->object  = $object;
        $this->causer  = $causer;
        $this->userIds = $userIds;
    }
}
