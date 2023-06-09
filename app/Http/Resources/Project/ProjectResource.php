<?php

namespace App\Http\Resources\Project;

use App\Models\ProjectStatus;
use App\Models\TaskStatus;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray ($request) : array|JsonSerializable|Arrayable
    {
        $data = [
            'id'             => $this->id,
            'name'           => $this->name,
            'customer_name'  => $this->whenHas('customer_name'),
            'code'           => $this->whenHas('code'),
            'summary'        => $this->whenHas('summary'),
            'starts_at'      => $this->starts_at->format('Y-m-d'),
            'ends_at'        => $this->ends_at->format('Y-m-d'),
            'duration'       => $this->duration,
            'status'         => $this->status,
            'progress'       => $this->progress,
            'pending_reason' => $this->whenHas('pending_reason'),
            'users'          => $this->whenLoaded('users'),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'can_delete'     => $this->can_delete,
        ];

        $tasks_count  = 0;
        $taskStatuses = TaskStatus::all();
        foreach (TaskStatus::STATUSES as $id => $name)
        {
            $taskStatus  = $taskStatuses->firstWhere('id', $id);
            $tasks_count += $this->{"{$id}_tasks"};

            $data['tasks_count_by_status'][] = [
                'id'          => $id,
                'name'        => $name,
                'color'       => $taskStatus->color,
                'tasks_count' => $this->{"{$id}_tasks"},
            ];
        }
        $data['tasks_count'] = $tasks_count;

        return $data;
    }
}
