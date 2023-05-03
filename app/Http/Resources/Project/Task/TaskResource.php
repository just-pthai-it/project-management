<?php

namespace App\Http\Resources\Project\Task;

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
            'id'         => $this->id,
            'name'       => $this->name,
            'project_id' => $this->project_id,
            'status'     => $this->status,
            'users'      => $this->users,
        ];
    }
}
