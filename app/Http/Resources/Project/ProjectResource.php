<?php

namespace App\Http\Resources\Project;

use App\Models\ProjectStatus;
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
            'code'           => $this->whenHas('code'),
            'summary'        => $this->whenHas('summary'),
            'starts_at'      => $this->starts_at->format('Y-m-d'),
            'ends_at'        => $this->ends_at->format('Y-m-d'),
            'duration'       => $this->duration,
            'status'         => $this->status,
            'progress'       => $this->progress,
            'pending_reason' => $this->whenHas('progress'),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];

        $tasks_count = 0;

        foreach (ProjectStatus::STATUSES as $id => $name)
        {
            $tasks_count                     += $this->{"{$id}_tasks"};
            $data['tasks_count_by_status'][] = [
                'id'          => $id,
                'name'        => $name,
                'tasks_count' => $this->{"{$id}_tasks"},
            ];
        }
        $data['tasks_count'] = $tasks_count;

        return $data;
    }
}
