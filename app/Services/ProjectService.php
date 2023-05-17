<?php

namespace App\Services;

use App\Events\SystemObjectAffected;
use App\Events\UserAssigned;
use App\Helpers\CusResponse;
use App\Http\Resources\ActivityLog\ActivityLogResource;
use App\Http\Resources\Project\ProjectCollection;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Project\ProjectSearchCollection;
use App\Http\Resources\Project\Task\TaskCollection;
use App\Http\Resources\Project\Task\TaskKanbanCollection;
use App\Http\Resources\Project\User\UserCollection;
use App\Http\Resources\Task\TaskSearchCollection;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Repositories\Contracts\ProjectRepositoryContract;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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

        return (new ProjectSearchCollection($projects))->response();
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
        $project->load(['users:id,name,avatar']);
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
            event(new UserAssigned($project, array_diff($inputs['user_ids'], [auth()->id()])));
        }
        event(new SystemObjectAffected($project, auth()->user(), 'created'));

        return CusResponse::createSuccessful(['id' => $project->id]);
    }

    public function update (Project $project, array $inputs) : JsonResponse
    {
        [$result, $message] = $this->__checkConditionBeforeUpdateProject($project, $inputs);
        if (!$result)
        {
            return CusResponse::failed([], $message);
        }

        $oldData = $project->getOriginal();
        $project->update($inputs);
        if (isset($inputs['user_ids']))
        {
            $newAssignee = $this->__assignUsers($project, $inputs['user_ids']);
            event(new UserAssigned($project, array_diff($newAssignee, [auth()->id()])));
        }
        event(new SystemObjectAffected($project, auth()->user(), 'updated', $oldData));

        return CusResponse::successful();
    }

    private function __assignUsers (Model $object, array $userIds) : array
    {
        $currentAssigneeIds = $object->users()->pluck('users.id')->all();
        $newAssignee        = array_diff($userIds, $currentAssigneeIds);
        $oldAssignee        = array_diff($currentAssigneeIds, $userIds);
        $object->users()->attach($newAssignee);
        $object->users()->detach($oldAssignee);

        return $newAssignee;
    }

    private function __checkConditionBeforeUpdateProject (Project $project, array $inputs) : array
    {
        if (isset($inputs['status_id']) && $inputs['status_id'] == ProjectStatus::STATUS_COMPLETE)
        {
            if (!$this->__checkIfCanUpdateProjectToCompleteStatus($project))
            {
                return [false, 'Không thể thực hiện hành động do vẫn còn đầu việc chưa hoàn thành.'];
            }
        }

        if (isset($inputs['status_id']) &&
            $project->status_id == ProjectStatus::STATUS_BEHIND_SCHEDULE &&
            in_array($inputs['status_id'], [ProjectStatus::STATUS_NOT_START, ProjectStatus::STATUS_IN_PROGRESS]))
        {
            if (!$this->__checkIfCanUpdateProjectStatusFromBehindSchedule($project, $inputs))
            {
                return [false, 'Không thể thực hiện hành động do ngày hiện tại đã vượt quá ngày kết thúc của dự án.'];
            }
        }

        if (isset($inputs['starts_at']) && $inputs['ends_at'])
        {
            if ($this->__checkIfAnyTasksTimeOverProjectTime($project, $inputs))
            {
                return [false, 'Không thể thực hiện hành động do phạm vi ngày bắt đầu và ngày kết thúc không bao quát hết tất cả đầu việc.'];
            }
        }

        return [true, 'OK'];
    }

    private function __checkIfCanUpdateProjectToCompleteStatus (Project $project) : bool
    {
        return $project->progress == 100;
    }

    private function __checkIfCanUpdateProjectStatusFromBehindSchedule (Project $project, array $inputs) : bool
    {
        return ($project->ends_at->format('Y-m-d') > now()->format('Y-m-d')) ||
               (isset($inputs['starts_at']) && $inputs['starts_at'] > now()->format('Y-m-d'));
    }

    private function __checkIfAnyTasksTimeOverProjectTime (Project $project, array $inputs) : bool
    {
        $newProject = $project->replicate()->fill(Arr::only($inputs, ['starts_at', 'ends_at']));
        return $project->tasks()->where('tasks.starts_at', '<', $newProject->starts_at_with_time)
                       ->orWhere('tasks.ends_at', '>', $newProject->ends_at_with_time)->exists();
    }

    public function delete (Project $project) : JsonResponse
    {
        $project->delete();
        $project->tasks()->delete();
        event(new SystemObjectAffected($project, auth()->user(), 'deleted'));
        return CusResponse::successful();
    }

    public function searchTasks (Project $project, array $inputs = []) : JsonResponse
    {
        $tasks = $project->tasks()->filter($inputs)->get(['id', 'name', 'project_id']);
        return (new TaskSearchCollection($tasks))->response();
    }

    public function listTasks (Project $project, array $inputs = []) : JsonResponse
    {
        $tasks = $project->tasks()->filter($inputs)
                         ->with(['status', 'users:id,name,avatar'])
                         ->get(['id', 'name', 'project_id', 'status_id', 'starts_at', 'ends_at']);
        return (new TaskCollection($tasks))->response();
    }

    public function listTasksKanban (Project $project, array $inputs = []) : JsonResponse
    {
        $tasks = $project->tasks()->filter($inputs)
                         ->with(['users:id,name,avatar'])
                         ->get(['id', 'name', 'project_id', 'status_id', 'starts_at', 'ends_at']);
        $tasks = $this->__groupTasksByStatus($tasks);
        return (new TaskKanbanCollection($tasks))->response();
    }

    private function __groupTasksByStatus (Collection $tasks) : Collection
    {
        return $tasks->groupBy('status_id');
    }

    public function getTask (Project $project, Task $task) : JsonResponse
    {
        $task->project = $project;
        $task->load(['status', 'files:id,name,url,fileable_type,fileable_id', 'taskUserPairs:id,task_id',
                     'taskUserPairs.file', 'children', 'parent']);
        return CusResponse::successful($task);
    }

    public function storeTask (Project $project, array $inputs) : JsonResponse
    {
        $task = $project->tasks()->create($inputs);
        if (isset($inputs['user_ids']))
        {
            $project->users()->attach($inputs['user_ids']);
            event(new UserAssigned($project, array_diff($inputs['user_ids'], [auth()->id()])));
        }
        $this->__updateProjectTimeAccordingToTask($project, $task);
        $this->__updateProjectProgress($project, $task);
        $project->save();
        event(new SystemObjectAffected($task, auth()->user(), 'created'));

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
        if ($completeTasksCount == 0)
        {
            $project->progress = 0;
            return;
        }

        $tasksCount = $project->tasks()->count();

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
        [$result, $message] = $this->__checkConditionBeforeUpdateTask($task, $inputs);
        if (!$result)
        {
            return CusResponse::failed([], $message);
        }

        $oldTask = $task->replicate();
        $task->update($inputs);
        if (isset($inputs['user_ids']))
        {
            $newAssignee = $this->__assignUsers($task, $inputs['user_ids']);
            event(new UserAssigned($project, array_diff($newAssignee, [auth()->id()])));
        }
        $this->__updateProjectTimeAccordingToTask($project, $task);
        $this->__updateProjectProgress($project, $task, $oldTask);
        $project->save();

        event(new SystemObjectAffected($task, auth()->user(), 'updated', $oldTask->getOriginal()));

        return CusResponse::successful();
    }

    private function __checkConditionBeforeUpdateTask (Task $task, array $inputs) : array
    {
        if (isset($inputs['status_id']) && $inputs['status_id'] == TaskStatus::STATUS_COMPLETE)
        {
            if (!$this->__checkIfCanUpdateTaskToCompleteStatus($task))
            {
                return [false, 'Không thể thực hiện được hành động do chưa hoàn thành hết các đầu việc con.'];
            }
        }

        if (isset($inputs['status_id']) &&
            $task->status_id == TaskStatus::STATUS_BEHIND_SCHEDULE &&
            in_array($inputs['status_id'], [TaskStatus::STATUS_NOT_START, TaskStatus::STATUS_IN_PROGRESS]))
        {
            if (!$this->__checkIfCanUpdateTaskStatusFromBehindSchedule($task, $inputs))
            {
                return [false, 'Không thể thực hiện được hành động do thời gian hiện tại đã vượt quá thời gian kết thúc của đầu việc.'];
            }
        }

        return [true, 'OK'];
    }

    private function __checkIfCanUpdateTaskToCompleteStatus (Task $task) : bool
    {
        return !$task->children()->where('status_id', '!=', TaskStatus::STATUS_COMPLETE)->exists();
    }

    private function __checkIfCanUpdateTaskStatusFromBehindSchedule (Task $task, array $inputs) : bool
    {
        return ($task->ends_at->format('Y-m-d') > now()->format('Y-m-d')) ||
               (isset($inputs['starts_at']) && $inputs['starts_at'] > now()->format('Y-m-d'));
    }

    public function deleteTask (Project $project, Task $task) : JsonResponse
    {
        $task->delete();
        $task->children()->delete();
        $this->__updateProjectProgress($project);
        event(new SystemObjectAffected($task, auth()->user(), 'deleted'));
        return CusResponse::successfulWithNoData();
    }

    public function listUsers (Project $project, array $inputs = []) : JsonResponse
    {
        $users = $project->users()->filter($inputs)->get(['users.id', 'users.name', 'users.email']);
        return (new UserCollection($users))->response();
    }

    public function history (Project $project) : JsonResponse
    {
        return ActivityLogResource::collection($project->activityLogs)->response();
    }
}
