<?php

namespace App\Http\Resources\Notification;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class NotificationResource extends JsonResource
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
            'id'                   => $this->id,
            'title'                => $this->title,
            'content'              => $this->content,
            'action'               => $this->action,
            'created_at'           => $this->created_at,
            'created_at_for_human' => $this->created_at_for_human,
            'read_at'              => $this->pivot->read_at,
        ];
    }
}
