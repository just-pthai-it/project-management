<?php

namespace App\CommandBus\Handlers\Project;

use App\CommandBus\Actions\Project\GenerateWithCountTasksByStatusQuery;
use App\CommandBus\Commands\Project\PaginateProjectsListCommand;
use App\CommandBus\Handler\Handler;
use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginateProjectsListHandler implements Handler
{
    private GenerateWithCountTasksByStatusQuery $generateWithCountTasksByStatusQuery;

    /**
     * @param GenerateWithCountTasksByStatusQuery $generateWithCountTasksByStatusQuery
     */
    public function __construct (GenerateWithCountTasksByStatusQuery $generateWithCountTasksByStatusQuery)
    {
        $this->generateWithCountTasksByStatusQuery = $generateWithCountTasksByStatusQuery;
    }

    /**
     * @param PaginateProjectsListCommand $command
     * @return LengthAwarePaginator
     */
    public function handle ($command) : LengthAwarePaginator
    {
        $currentUser = auth()->user();
        $queryParams = $command->getQueryParams();;
        $withCountTaskByStatusQuery = ($this->generateWithCountTasksByStatusQuery)();

        if ($currentUser->tokenCan('*') || $currentUser->tokenCan('statistical:project'))
        {
            $projects = $this->__paginateProjectByRootUser($queryParams, $withCountTaskByStatusQuery);
        }
        else
        {
            $projects = $this->__paginateProjectByNonRootUser($queryParams, $withCountTaskByStatusQuery);
        }

        return $projects;
    }

    private function __paginateProjectByNonRootUser (array $queryParams, array $withCountTaskByStatusQuery) : LengthAwarePaginator
    {
        return auth()->user()->assignedProjects()
                     ->orWhere('projects.user_id', '=', auth()->id())
                     ->filter($queryParams)
                     ->with('status')->withCount($withCountTaskByStatusQuery)
                     ->paginate($queryParams['per_page'] ?? 10);

    }

    private function __paginateProjectByRootUser (array $queryParams, array $withCountTaskByStatusQuery) : LengthAwarePaginator
    {
        return Project::query()->filter($queryParams)
                      ->with(['status'])->withCount($withCountTaskByStatusQuery)
                      ->paginate($queryParams['per_page'] ?? 10);
    }

}