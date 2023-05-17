<?php

namespace App\Http\Resources\Project\Task;

use App\Http\Resources\File\FileResource;
use App\Http\Resources\Task\TaskReportResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray ($request) : array|JsonSerializable|Arrayable
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'project_id'     => $this->project_id,
            'description'    => $this->description,
            'starts_at'      => $this->starts_at,
            'ends_at'        => $this->ends_at,
            'status'         => $this->status,
            'pending_reason' => $this->pending_reason,
            'project'        => $this->project,
            'files'          => FileResource::collection($this->files),
            'reports'        => TaskReportResource::collection($this->taskUserPairs->whereNotNull('file')),
            'children'       => $this->children,
            'parent'         => $this->parent,
            'users'          => $this->users,
        ];
    }
}
