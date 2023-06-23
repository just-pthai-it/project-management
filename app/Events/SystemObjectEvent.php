<?php

namespace App\Events;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SystemObjectEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Project|Task $object;
    public User $currentUser;
    public string $action;
    public array $oldData;
    public array $dataChanges;

    /**
     * @param Project|Task $object
     * @param User         $causer
     * @param string       $action
     * @param array        $oldData
     * @param array        $dataChanges
     */
    public function __construct (Project|Task $object, User $causer, string $action, array $oldData = [], array $dataChanges = [])
    {
        $this->object      = $object;
        $this->currentUser = $causer;
        $this->action      = $action;
        $this->oldData     = $oldData;
        $this->dataChanges = $dataChanges;
    }
}
