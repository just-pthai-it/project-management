<?php

namespace App\CommandBus\Handlers\Project;

use App\CommandBus\Actions\AssignUsers;
use App\CommandBus\Actions\CheckIfCanUpdateProject;
use App\CommandBus\Commands\Project\UpdateProjectCommand;
use App\CommandBus\Handler\Handler;
use App\Events\SystemObjectEvent;

class UpdateProjectHandler implements Handler
{
    private AssignUsers $assignUsers;
    private CheckIfCanUpdateProject $checkIfCanUpdateProject;

    /**
     * @param AssignUsers             $assignUsers
     * @param CheckIfCanUpdateProject $checkIfCanUpdateProject
     */
    public function __construct (AssignUsers $assignUsers, CheckIfCanUpdateProject $checkIfCanUpdateProject)
    {
        $this->assignUsers             = $assignUsers;
        $this->checkIfCanUpdateProject = $checkIfCanUpdateProject;
    }

    /**
     * @param UpdateProjectCommand $command
     * @return void
     */
    public function handle ($command) : void
    {
        $currentUser = auth()->user();
        $project     = $command->getProject();
        $oldData     = $project->getOriginal();
        $project->fill($this->__getProjectAttributes($command))->syncChanges();

        if ($project->wasChanged())
        {
            ($this->checkIfCanUpdateProject)($project, $oldData);
            $project->save();
            event(new SystemObjectEvent($project, $currentUser, 'updated', $oldData, $project->getChanges()));
        }
        ($this->assignUsers)($project, $command->getUserIds());
    }

    private function __getProjectAttributes (UpdateProjectCommand $command) : array
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