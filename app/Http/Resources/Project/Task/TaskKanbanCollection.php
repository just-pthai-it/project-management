<?php

namespace App\Http\Resources\Project\Task;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class TaskKanbanCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray ($request) : array|JsonSerializable|Arrayable
    {
        $this->collection->transform(function ($item, $key)
        {
            return [
                'status_id' => (int)$key,
                'tasks'     => $item,
            ];
        });

        return $this->collection->sortBy('status_id');
    }
}
