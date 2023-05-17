<?php

namespace App\Http\Resources\Task;

use App\Http\Resources\File\FileResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class TaskReportResource extends JsonResource
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
            'id'   => $this->id,
            'user' => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'file' => new FileResource($this->file),
            'is_editable' => $this->user->id == auth()->id(),
        ];
    }
}
