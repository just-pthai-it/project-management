<?php

namespace App\CommandBus\Handlers\Project;

use App\CommandBus\Actions\AssignUsers;
use App\CommandBus\Commands\Project\CreateProjectCommand;
use App\CommandBus\Handler\Handler;
use App\Events\SystemObjectEvent;
use App\Models\Project;

class CreateProjectHandler implements Handler
{
    private AssignUsers $assignUsers;

    /**
     * @param AssignUsers $assignUsers
     */
    public function __construct (AssignUsers $assignUsers)
    {
        $this->assignUsers = $assignUsers;
    }

    /**
     * @param CreateProjectCommand $command
     * @return Project
     */
    public function handle ($command) : Project
    {
        $currentUser = auth()->user();
        $project     = $currentUser->projects()->create($this->__getProjectAttributes($command));
        ($this->assignUsers)($project, $command->getUserIds());
        event(new SystemObjectEvent($project, $currentUser, 'created'));

        return $project;
    }

    private function __getProjectAttributes (CreateProjectCommand $command) : array
    {
        return [
            'name'           => $command->getName(),
            'customer_name'  => $command->getCustomerName(),
            'code'           => $command->getCode(),
            'summary'        => $command->getSummary(),
            'starts_at'      => $command->getStartsAt(),
            'ends_at'        => $command->getEndsAt(),
            'duration'       => $command->getDuration(),
            'status_id'      => $command->getStatusId(),
            'pending_reason' => $command->getPendingReason(),
        ];
    }
}