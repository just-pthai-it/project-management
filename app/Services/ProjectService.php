<?php

namespace App\Services;

use App\Events\ObjectCreated;
use App\Events\ObjectUpdated;
use App\Events\UserAssigned;
use App\Helpers\CusResponse;
use App\Http\Resources\ActivityLog\ActivityLogResource;
use App\Http\Resources\Project\ProjectCollection;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Project\Task\TaskCollection;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Repositories\Contracts\ProjectRepositoryContract;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ProjectService implements Contracts\ProjectServiceContract
{
    private ProjectRepositoryContract $projectRepository;

    public function __construct (ProjectRepositoryContract $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function search (array $inputs = []) : JsonResponse
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

    private function __listByRootUser (array $inputs) : Collection
    {
        return $this->projectRepository->find(['id', 'name'], [], [], [], [['filter', $inputs]]);
    }

    private function __listByNonRootUser (array $inputs) : Collection
    {
        return auth()->user()->assignedProjects()->filter($inputs)->get(['projects.id', 'projects.name']);
    }

    public function list (array $inputs = []) : JsonResponse
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
        $project->users()->attach($inputs['assigned_user_ids']);
        event(new ObjectCreated($project, auth()->user()));
        event(new UserAssigned($project, array_diff($inputs['assigned_user_ids'], [auth()->id()])));
        return CusResponse::createSuccessful(['id' => $project->id]);
    }

    public function update (Project $project, array $inputs) : JsonResponse
    {
        $oldData = $project->getOriginal();
        $project->update($inputs);
        if (isset($inputs['assigned_user_ids']))
        {
            $project->users()->syncWithoutDetaching($inputs['assigned_user_ids']);
            event(new UserAssigned($project, array_diff($inputs['assigned_user_ids'], [auth()->id()])));
        }
        if (isset($inputs['unassigned_user_ids']))
        {
            $project->users()->detach(array_diff($inputs['unassigned_user_ids'], [auth()->id()]));
        }
        event(new ObjectUpdated($project, auth()->user(), $oldData));

        return CusResponse::successful();
    }

    public function delete (Project $project) : JsonResponse
    {
        $project->delete();
        return CusResponse::successful();
    }

    public function searchTasks (Project $project, array $inputs = []) : JsonResponse
    {
        $tasks = $project->tasks()->filter($inputs)->get(['id', 'name', 'project_id']);
        return CusResponse::successful($tasks);
    }

    public function listTasks (Project $project, array $inputs = []) : JsonResponse
    {
        $tasks = $project->tasks()->filter($inputs)
                         ->with(['status', 'users:id,name,avatar'])
                         ->get(['id', 'name', 'project_id', 'status_id']);
        return (new TaskCollection($tasks))->response();
    }

    public function getTask (Project $project, Task $task) : JsonResponse
    {
        $task->project = $project;
        $task->load(['status', 'files:id,name,url,fileable_type,fileable_id', 'taskUserPairs:id,task_id', 'taskUserPairs.file']);
        return CusResponse::successful($task);
    }

    public function storeTask (Project $project, array $inputs) : JsonResponse
    {
        $task = $project->tasks()->create($inputs);
        $task->users()->attach($inputs['assigned_user_ids']);
        event(new UserAssigned($task, array_diff($inputs['assigned_user_ids'], [auth()->id()])));
        $this->__updateProjectTimeAccordingToTask($project, $task);
        $this->__updateProjectProgress($project, $task);
        $project->save();
        event(new ObjectCreated($task, auth()->user()));

        return CusResponse::createSuccessful(['id' => $task->id]);
    }

    private function __updateProjectProgress (Project $project, ?Task $task = null, ?Task $oldTask = null) : void
    {
        if ($task != null && $oldTask != null)
        {
            if (($task->status_id == TaskStatus::STATUS_COMPLETE && $oldTask->status_id == TaskStatus::STATUS_COMPLETE) ||
                ($task->status_id != TaskStatus::STATUS_COMPLETE && $oldTask->status_id != TaskStatus::STATUS_COMPLETE))
            {
                return;
            }
        }

        $completeTasksCount = $project->tasks()->where('status_id', '=', TaskStatus::STATUS_COMPLETE)->count();
        $tasksCount         = $project->tasks()->count();

        $project->progress = $completeTasksCount / $tasksCount * 100;
    }

    private function __updateProjectTimeAccordingToTask (Project $project, Task $task) : void
    {
        if ($task->ends_at->format('Y-m-d') > $project->ends_at->format('Y-m-d') ||
            $task->starts_at->format('Y-m-d') < $project->starts_at->format('Y-m-d'))
        {
            $project->update(Arr::only($task->getOriginal(), ['starts_at', 'ends_at', 'duration']));
        }
    }

    public function updateTask (Project $project, Task $task, array $inputs) : JsonResponse
    {
        $oldTask = $task->replicate();
        $task->update($inputs);
        if (isset($inputs['assigned_user_ids']))
        {
            $task->users()->syncWithoutDetaching($inputs['assigned_user_ids']);
            event(new UserAssigned($task, array_diff($inputs['assigned_user_ids'], [auth()->id()])));
        }
        if (isset($inputs['unassigned_user_ids']))
        {
            $task->users()->detach(array_diff($inputs['unassigned_user_ids'], [auth()->id()]));
        }
        $this->__updateProjectTimeAccordingToTask($project, $task);
        $this->__updateProjectProgress($project, $task, $oldTask);
        $project->save();

        event(new ObjectUpdated($task, auth()->user(), $oldTask->getOriginal()));

        return CusResponse::successful();
    }

    public function deleteTask (Project $project, Task $task) : JsonResponse
    {
        $task->delete();
        $this->__updateProjectProgress($project);
        return CusResponse::successfulWithNoData();
    }

    public function history (Project $project) : JsonResponse
    {
        return ActivityLogResource::collection($project->activityLogs)->response();
    }
}
