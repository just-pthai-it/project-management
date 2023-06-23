<?php

namespace App\CommandBus\Commands\Project;

use App\Models\Project;

class UpdateProjectCommand
{
    private Project $project;
    private string $name;
    private string $customerName;
    private string $code;
    private string|null $summary;
    private string $starts_at;
    private string $ends_at;
    private int $duration;
    private int $statusId;
    private string|null $pendingReason;
    private array $userIds;

    /**
     * @param array   $input
     * @param Project $project
     */
    public function __construct (Project $project, array $input)
    {
        $this->project       = $project;
        $this->name          = $input['name'];
        $this->customerName  = $input['customer_name'];
        $this->code          = $input['code'];
        $this->summary       = $input['summary'] ?? null;
        $this->starts_at     = $input['starts_at'];
        $this->ends_at       = $input['ends_at'];
        $this->duration      = $input['duration'];
        $this->statusId      = $input['status_id'];
        $this->pendingReason = $input['pending_reason'] ?? null;
        $this->userIds       = $input['user_ids'];
    }

    /**
     * @return Project
     */
    public function getProject () : Project
    {
        return $this->project;
    }

    /**
     * @return string
     */
    public function getName () : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCustomerName () : string
    {
        return $this->customerName;
    }

    /**
     * @return string
     */
    public function getCode () : string
    {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getSummary () : ?string
    {
        return $this->summary;
    }

    /**
     * @return string
     */
    public function getStartsAt () : string
    {
        return $this->starts_at;
    }

    /**
     * @return string
     */
    public function getEndsAt () : string
    {
        return $this->ends_at;
    }

    /**
     * @return int
     */
    public function getDuration () : int
    {
        return $this->duration;
    }

    /**
     * @return int
     */
    public function getStatusId () : int
    {
        return $this->statusId;
    }

    /**
     * @return string|null
     */
    public function getPendingReason () : ?string
    {
        return $this->pendingReason;
    }

    /**
     * @return array
     */
    public function getUserIds () : array
    {
        return $this->userIds;
    }
}