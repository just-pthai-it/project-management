<?php

namespace App\Http\Resources\Project\Task;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TaskKanbanCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray ($request)
    {
        $this->collection->transform(function ($item, $key)
        {
            return [
                'status_id' => (int)$key,
                'tasks'     => $item,
            ];
        });

        return $this->collection;
    }
}
