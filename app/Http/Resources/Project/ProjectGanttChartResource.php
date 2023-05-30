<?php

namespace App\Http\Resources\Project;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ProjectGanttChartResource extends JsonResource
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
            'id'       => $this->id,
            'name'     => $this->name,
            'progress' => $this->progress,
            'type'     => 'project',
            'start'    => $this->starts_at,
            'end'      => $this->ends_at,
        ];
    }
}
