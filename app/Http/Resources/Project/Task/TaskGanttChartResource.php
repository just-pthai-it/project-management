<?php

namespace App\Http\Resources\Project\Task;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskGanttChartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray ($request)
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'type'         => 'task',
            'dependencies' => $this->when($this->parent_id != null, [$this->parent_id]),
            'start'        => $this->starts_at,
            'end'          => $this->ends_at,
        ];
    }
}
