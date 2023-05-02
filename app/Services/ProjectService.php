<?php

namespace App\Services;

use App\Helpers\CusResponse;
use App\Http\Resources\Project\ProjectCollection;
use App\Http\Resources\Project\ProjectResource;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Repositories\Contracts\ProjectRepositoryContract;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectService implements Contracts\ProjectServiceContract
{
    private ProjectRepositoryContract $projectRepository;

    public function __construct (ProjectRepositoryContract $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function list (array $inputs = []) : JsonResponse
    {
        if (isset($inputs['page']))
        {
            $withCountTaskByStatusQuery = $this->__generateQueryWithCountTasksByStatus();;

            if (auth()->user()->isRoot())
            {
                $projects = $this->__paginateProjectByRootUser($inputs, $withCountTaskByStatusQuery);
            }
            else
            {
                $projects = $this->__paginateProjectByNonRootUser($inputs, $withCountTaskByStatusQuery);
            }

            return (new ProjectCollection($projects))->response();
        }
        else
        {
            if (auth()->user()->isRoot())
            {
                $projects = $this->__listByRootUser($inputs);
            }
            else
            {
                $projects = $this->__listByNonRootUser($inputs);
            }

            return CusResponse::successful($projects);
        }
    }

    private function __paginateProjectByNonRootUser (array $inputs, array $withCountTaskByStatusQuery) : LengthAwarePaginator
    {
        return auth()->user()->assignedProjects()
                     ->filter($inputs)
                     ->with('status')->withCount($withCountTaskByStatusQuery)
                     ->paginate($inputs['per_page'] ?? 10);

    }

    private function __paginateProjectByRootUser (array $inputs, array $withCountTaskByStatusQuery) : LengthAwarePaginator
    {
        return $this->projectRepository->paginate($inputs['per_page'] ?? 10, ['*'], [], [],
                                                  [['with', ['status']], ['filter', $inputs],
                                                   ['withCount', $withCountTaskByStatusQuery]]);
    }

    private function __listByRootUser (array $inputs) : Collection
    {
        return $this->projectRepository->find(['id', 'name'], [], [], [], [['filter', $inputs]]);
    }

    private function __listByNonRootUser (array $inputs) : Collection
    {
        return auth()->user()->assignedProjects()->filter($inputs)->get(['id', 'name']);
    }

    private function __generateQueryWithCountTasksByStatus () : array
    {
        $withCountArr = [];
        foreach (ProjectStatus::STATUSES as $id => $name)
        {
            $withCountArr["tasks as {$id}_tasks"] = function (Builder $query) use ($id)
            {
                $query->where('status_id', '=', $id);
            };
        }

        return $withCountArr;
    }

    public function get (Project $project, array $inputs = []) : JsonResponse
    {
        $withCountTaskByStatusQuery = $this->__generateQueryWithCountTasksByStatus();;
        $project->loadCount($withCountTaskByStatusQuery);
        return (new ProjectResource($project))->response();
    }

    /**
     * @throws Exception
     */
    public function store (array $inputs) : JsonResponse
    {
        $project = auth()->user()->projects()->create($inputs);
        $project->users()->attach(array_unique([...($inputs['user_ids'] ?? []), auth()->id()]));

        return CusResponse::successful($project);
    }

    public function update (Project $project, array $inputs) : JsonResponse
    {
        $project->update($inputs);
        if (isset($inputs['user_ids']))
        {
            $project->users()->sync($inputs['user_ids']);
        }

        return CusResponse::successfulWithNoData();
    }

    public function delete (Project $project) : JsonResponse
    {
        $project->delete();
        return CusResponse::successful();
    }
}
