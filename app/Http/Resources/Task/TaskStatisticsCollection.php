<?php

namespace App\Http\Resources\Task;

use App\Models\TaskStatus;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class TaskStatisticsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request) : array|JsonSerializable|Arrayable
    {
        foreach (TaskStatus::STATUSES as $id => $name)
        {
            if ($this->collection->doesntContain('status_id', $id))
            {
                $this->collection->push((object)['status_id' => $id, 'tasks_count' => 0]);
            }
        }

        $tasksCount   = 0;
        $taskStatuses = TaskStatus::all();
        $this->collection->transform(function ($groupTaskCount, $key) use ($taskStatuses, &$tasksCount)
        {
            $taskStatus = $taskStatuses->firstWhere('id', '=', $groupTaskCount->status_id);
            $tasksCount += $groupTaskCount->tasks_count;

            return [
                'id'             => $taskStatus->id,
                'name'           => $taskStatus->name,
                'color'          => $taskStatus->color,
                'tasks_count' => $groupTaskCount->tasks_count,
            ];
        });

        return $this->collection;
    }
}
