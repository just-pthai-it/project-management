<?php

namespace App\CommandBus\Actions;

use App\Models\Project;
use App\Models\ProjectStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class CheckIfCanUpdateProject
{
    public function __invoke (Project $project, array $oldData) : void
    {
        $this->__checkIfCanUpdateProjectToCompleteStatus($project);
        $this->__checkIfCanUpdateWhenBehindScheduleProjectStatusToWorkingStatus($project, $oldData);
        $this->__checkIfAnyTasksTimeOverProjectTime($project, $oldData);
    }

    private function __checkIfCanUpdateProjectToCompleteStatus (Project $project) : void
    {
        $dataChanges = $project->getChanges();
        if (isset($dataChanges['status_id']) && $project->status_id == ProjectStatus::STATUS_COMPLETE)
        {
            if ($project->progress != 100)
            {
                abort(422, 'Không thể thực hiện hành động do vẫn còn đầu việc chưa hoàn thành.');
            }
        }
    }

    private function __checkIfCanUpdateWhenBehindScheduleProjectStatusToWorkingStatus (Project $project, array $oldData) : void
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
                    abort(422, 'Không thể thực hiện hành động do ngày hiện tại đã vượt quá ngày kết thúc của dự án.');
                }
            }
            else if (Carbon::parse($oldData['ends_at'])->toDateString() < now('+7')->toDateString())
            {
                abort(422, 'Không thể thực hiện hành động do ngày hiện tại đã vượt quá ngày kết thúc của dự án.');
            }
        }
    }

    private function __checkIfAnyTasksTimeOverProjectTime (Project $project, array $oldData) : void
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
                abort(422,
                      'Không thể thực hiện hành động do phạm vi ngày bắt đầu và ngày kết thúc không bao quát hết tất cả đầu việc.');
            }
        }
    }
}