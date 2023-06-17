<?php

namespace App\Services;

use App\Events\SystemObjectEvent;
use App\Events\UserAssignedEvent;
use App\Helpers\Constants;
use App\Helpers\CusResponse;
use App\Http\Resources\ActivityLog\ActivityLogResource;
use App\Http\Resources\Project\ProjectCollection;
use App\Http\Resources\Project\ProjectGanttChartResource;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Project\ProjectSearchCollection;
use App\Http\Resources\Project\ProjectStatisticsCollection;
use App\Http\Resources\Project\Task\TaskGanttChartResource;
use App\Http\Resources\Project\Task\TaskKanbanCollection;
use App\Http\Resources\Project\Task\TaskResource;
use App\Http\Resources\Project\User\UserCollection;
use App\Http\Resources\Task\TaskSearchCollection;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryContract;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProjectService implements Contracts\ProjectServiceContract
{
    private ProjectRepositoryContract $projectRepository;

    public function __construct (ProjectRepositoryContract $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function search (array $inputs = []) : JsonResponse
    {
        if (auth()->user()->tokenCan('*') || auth()->user()->tokenCan('statistical:project'))
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

        if (auth()->user()->tokenCan('*') || auth()->user()->tokenCan('statistical:project'))
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
                     ->orWhere('projects.user_id', '=', auth()->id())
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
        foreach (TaskStatus::STATUSES as $id => $name)
        {
            $withCountArr["tasks as {$id}_tasks"] = function (Builder $query) use ($id)
            {
                $query->where('status_id', '=', $id);
            };
        }

        return $withCountArr;
    }

    public function listGanttChart (array $inputs = []) : JsonResponse
    {
        if (auth()->user()->tokenCan('*') || auth()->user()->tokenCan('statistical:project'))
        {
            $query = Project::query();
        }
        else
        {
            $query = auth()->user()->assignedProjects();
        }

        $projects = $query->when(empty($inputs), fn (Builder $query) => $query->limit(Constants::DEFAULT_PER_PAGE))
                          ->filter($inputs)->get();

        return ProjectGanttChartResource::collection($projects)->response();
    }

    public function statistics (array $inputs) : JsonResponse
    {
        if (!isset($inputs['start_at']) && !isset($inputs['end_at']))
        {
            $inputs = ['start_at' => Carbon::now()->firstOfMonth(), 'end_at' => Carbon::now()->endOfMonth()];
        }

        if (!isset($inputs['start_at']) || !isset($inputs['end_at']))
        {
            abort(400);
        }

        $groupProjectsCount = DB::table('projects')
                                ->where('starts_at', '>=', $inputs['start_at'])
                                ->where('ends_at', '<=', $inputs['end_at'])
                                ->groupBy('status_id')
                                ->selectRaw('count(*) as projects_count, status_id')->get();

        return (new ProjectStatisticsCollection($groupProjectsCount))->response();
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
        $this->__assignUsers($project, $inputs['user_ids'] ?? []);
        event(new SystemObjectEvent($project, auth()->user(), 'created'));

        return CusResponse::createSuccessful(['id' => $project->id]);
    }


    /**
     * @throws Exception
     */
    public function update (Project $project, array $inputs) : JsonResponse
    {
        try
        {
            DB::beginTransaction();
            $oldData = $project->getOriginal();
            $project->update($inputs);
            if ($project->wasChanged())
            {
                $this->__checkIfUpdateActionIsValid($project, $oldData);
                event(new SystemObjectEvent($project, auth()->user(), 'updated', $oldData, $project->getChanges()));
            }
            $this->__assignUsers($project, $inputs['user_ids'] ?? []);

            DB::commit();
            return CusResponse::successful();
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }
    }

    private function __assignUsers (Model $object, array $userIds) : void
    {
        if (empty($userIds))
        {
            return;
        }

        $currentAssigneeIds = $object->wasRecentlyCreated ? [] : $object->users()->pluck('users.id')->all();
        $newAssigneeIds     = array_diff($userIds, $currentAssigneeIds);
        $oldAssigneeIds     = array_diff($currentAssigneeIds, $userIds);
        $object->users()->attach($newAssigneeIds);
        $object->users()->detach($oldAssigneeIds);

        if ($object instanceof Project && !$object->wasRecentlyCreated && !empty($oldAssigneeIds))
        {
            $this->__unassignUserFromTaskAfterUnassignedFromProject($object, $oldAssigneeIds);
        }

        if (!empty($newAssigneeIds))
        {
            event(new UserAssignedEvent($object, auth()->user(), array_diff($newAssigneeIds, [auth()->id()])));
        }
    }

    private function __unassignUserFromTaskAfterUnassignedFromProject (Project $project, array $oldAssigneeIds) : void
    {
        foreach ($project->tasks as $task)
        {
            $task->users()->detach($oldAssigneeIds);
        }
    }

    private function __checkIfUpdateActionIsValid (Project $project, array $oldData) : void
    {
        $results   = [];
        $results[] = $this->__checkIfCanUpdateProjectToCompleteStatus($project);
        $results[] = $this->__checkIfCanUpdateProjectStatusFromBehindScheduleToOthersStatus($project,
                                                                                            $oldData);
        $results[] = $this->__checkIfAnyTasksTimeOverProjectTime($project, $oldData);

        foreach ($results as $result)
        {
            [$isValid, $message] = $result;
            if (!$isValid)
            {
                abort(422, $message);
            }
        }
    }

    private function __checkIfCanUpdateProjectToCompleteStatus (Project $project) : array
    {
        $dataChanges = $project->getChanges();
        if (isset($dataChanges['status_id']) && $project->status_id == ProjectStatus::STATUS_COMPLETE)
        {
            if ($project->progress != 100)
            {
                return [false, 'Không thể thực hiện hành động do vẫn còn đầu việc chưa hoàn thành.'];
            }

        }

        return [true, 'Ok'];
    }

    private function __checkIfCanUpdateProjectStatusFromBehindScheduleToOthersStatus (Project $project, array $oldData) : array
    {
        $dataChanges = $project->getChanges();
        if (isset($dataChanges['status_id']) &&
            $oldData['status_id'] == ProjectStatus::STATUS_BEHIND_SCHEDULE &&
            in_array($project->status_id, [ProjectStatus::STATUS_NOT_START, ProjectStatus::STATUS_IN_PROGRESS]))
        {
            if (isset($dataChanges['ends_at']))
            {
                if ($project->ends_at->toDateString() < now('+7')->toDateString())
                {
                    return [false, 'Không thể thực hiện hành động do ngày hiện tại đã vượt quá ngày kết thúc của dự án.'];
                }
            }
            else if (Carbon::parse($oldData['ends_at'])->toDateString() < now('+7')->toDateString())
            {
                return [false, 'Không thể thực hiện hành động do ngày hiện tại đã vượt quá ngày kết thúc của dự án.'];
            }
        }

        return [true, 'Ok'];
    }

    private function __checkIfAnyTasksTimeOverProjectTime (Project $project, array $oldData) : array
    {
        $dataChanges = $project->getChanges();
        if (isset($dataChanges['starts_at']) || isset($dataChanges['ends_at']))
        {
            $isExists = $project->tasks()->where(function (Builder $query) use ($project)
            {
                $query->where('tasks.starts_at', '<', $project->starts_at_with_time)
                      ->orWhere('tasks.ends_at', '>', $project->ends_at_with_time);
            })->exists();

            if ($isExists)
            {
                return [false, 'Không thể thực hiện hành động do phạm vi ngày bắt đầu và ngày kết thúc không bao quát hết tất cả đầu việc.'];
            }
        }

        return [true, 'Ok'];
    }

    public function delete (Project $project) : JsonResponse
    {
        $project->delete();
        $project->tasks()->delete();
        event(new SystemObjectEvent($project, auth()->user(), 'deleted'));
        return CusResponse::successful();
    }

    public function searchTasks (Project $project, array $inputs = []) : JsonResponse
    {
        $tasks = Task::query()
                     ->where('project_id', '=', $project->id)
                     ->filter($inputs)->get(['id', 'name', 'project_id']);
        return (new TaskSearchCollection($tasks))->response();
    }

    public function listTasks (Project $project, array $inputs = []) : JsonResponse
    {
        $tasks = $project->tasks()->filter($inputs)
                         ->with(['status', 'users:id,name,avatar'])
                         ->get(['id', 'name', 'project_id', 'status_id', 'starts_at', 'ends_at']);
        return (TaskResource::collection($tasks))->response();
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
        $task = $tasks->groupBy('status_id');
        foreach (TaskStatus::STATUSES as $id => $name)
        {
            if ($task->doesntContain(function (Collection $value, int $key) use ($id)
            {
                return $id == $key;
            }))
            {
                $task->put($id, Collection::make());
            }
        }
        return $task;
    }

    public function listTasksGanttChart (Project $project, array $inputs = []) : JsonResponse
    {
        $tasks = $project->tasks()->filter($inputs)
                         ->get(['id', 'name', 'project_id', 'status_id', 'parent_id', 'starts_at', 'ends_at']);
        return TaskGanttChartResource::collection($tasks)->response();
    }

    public function getTask (Project $project, Task $task) : JsonResponse
    {
        $task->project = $project;
        $task->load(['status', 'files:id,name,url,fileable_type,fileable_id', 'taskUserPairs:id,task_id,user_id',
                     'taskUserPairs.file', 'taskUserPairs.user', 'users:id,name,email,avatar', 'children.status', 'parent.status']);
        return (new TaskResource($task))->response();
    }

    public function storeTask (Project $project, array $inputs) : JsonResponse
    {
        $task = $project->tasks()->create($inputs);
        $this->__assignUsers($task, $inputs['user_ids'] ?? []);
        $this->__updateProjectTimeAccordingToTask($project, $task);
        $this->__updateProjectProgress($project);
        $project->save();
        event(new SystemObjectEvent($task, auth()->user(), 'created'));

        return CusResponse::createSuccessful(['id' => $task->id]);
    }

    private function __updateProjectProgress (Project $project) : void
    {
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

        $oldData = $task->getOriginal();
        $task->update($inputs);
        $this->__assignUsers($task, $inputs['user_ids'] ?? []);

        if ($task->wasChanged())
        {
            $this->__updateProjectAccordingToTaskChanges($project, $task, $oldData);
            event(new SystemObjectEvent($task, auth()->user(), 'updated', $oldData, $task->getChanges()));
        }

        return CusResponse::successful();
    }

    private function __updateProjectAccordingToTaskChanges (Project $project, Task $task, array $oldData) : void
    {
        $dataChanges = $task->getChanges();
        if (isset($dataChanges['starts_at']) || isset($dataChanges['ends_at']))
        {
            $this->__updateProjectTimeAccordingToTask($project, $task);
        }
        if (isset($dataChanges['status_id']) &&
            in_array(TaskStatus::STATUS_COMPLETE, [$oldData['status_id'], $task->status_id]))
        {
            $this->__updateProjectProgress($project);
        }

        $project->save();
    }

    private function __checkConditionBeforeUpdateTask (Task $task, array $inputs) : array
    {
        if (isset($inputs['status_id']) && $inputs['status_id'] == TaskStatus::STATUS_COMPLETE)
        {
            if ($task->status_id != TaskStatus::STATUS_REVIEW)
            {
                return [false, 'Đầu việc cần phải được duyệt qua trước khi cập nhật trạng thái Hoàn thành.'];
            }

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
        if (isset($inputs['ends_at']))
        {
            return Carbon::parse($inputs['ends_at'])->toDateTimeString() > now('+7')->toDateTimeString();
        }

        return $task->ends_at->toDateTimeString() > now('+7')->toDateTimeString();
    }

    public function deleteTask (Project $project, Task $task) : JsonResponse
    {
        $task->delete();
        $task->children()->delete();
        $this->__updateProjectProgress($project);
        event(new SystemObjectEvent($task, auth()->user(), 'deleted'));
        return CusResponse::successfulWithNoData();
    }

    public function listUsers (Project $project, array $inputs = []) : JsonResponse
    {
        $users = $project->users()->filter($inputs)->get(['users.id', 'users.name', 'users.email']);
        return (new UserCollection($users))->response();
    }

    public function history (Project $project) : JsonResponse
    {
        $project->load(['activityLogs.user:id,name,email,avatar']);
        return ActivityLogResource::collection($project->activityLogs)->response();
    }
}
