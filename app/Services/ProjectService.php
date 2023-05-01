<?php

namespace App\Services;

use App\Helpers\CusResponse;
use App\Http\Resources\Project\ProjectCollection;
use App\Http\Resources\Project\ProjectResource;
use App\Models\ProjectStatus;
use App\Repositories\Contracts\ProjectRepositoryContract;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

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
            $withCountTasksQuery = $this->__generateQueryWithCountTasksByStatus();;
            $projects = $this->projectRepository->paginate($inputs['per_page'] ?? 10, ['*'], [], [],
                                                           [['with', ['status']], ['filter', $inputs],
                                                            ['withCount', $withCountTasksQuery]]);
            return (new ProjectCollection($projects))->response();
        }
        else
        {
            $projects = $this->projectRepository->find(['id', 'name'], [], [], [], [['filter', $inputs]]);
            return CusResponse::successful($projects);
        }
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

    public function get (int $id, array $inputs = []) : JsonResponse
    {
        $withCountTasksQuery = $this->__generateQueryWithCountTasksByStatus();;
        $project = $this->projectRepository->findById($id, ['*'], [['withCount', $withCountTasksQuery]]);
        return (new ProjectResource($project))->response();
    }

    /**
     * @throws Exception
     */
    public function store (array $inputs) : JsonResponse
    {
        $project = auth()->user()->projects()->create($inputs);
        if (isset($inputs['user_ids']))
        {
            $project->users()->attach($inputs['user_ids']);
        }

        return CusResponse::successful($project);
    }

    public function update (int $id, array $inputs) : void
    {
        $this->projectRepository->updateById($id, $inputs);
        if (isset($inputs['user_ids']))
        {
            $this->projectRepository->syncPivots($id, 'users', $inputs['user_ids']);
        }
    }

    public function delete (int $id) : JsonResponse
    {
        $this->projectRepository->deleteById($id);
        return CusResponse::successful();
    }
}
