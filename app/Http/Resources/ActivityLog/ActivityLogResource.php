<?php

namespace App\Http\Resources\ActivityLog;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ActivityLogResource extends JsonResource
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
            'id'          => $this->id,
            'description' => $this->description,
            'comment'     => $this->whenLoaded('comment'),
            'created_at'  => $this->created_at,
        ];
    }
}
