<?php

namespace App\CommandBus\Commands\Project;

class PaginateProjectsListCommand
{
    private array $queryParams;

    /**
     * @param array $queryParams
     */
    public function __construct (array $queryParams)
    {
        $this->queryParams = $queryParams;
    }

    /**
     * @return array
     */
    public function getQueryParams () : array
    {
        return $this->queryParams;
    }
}