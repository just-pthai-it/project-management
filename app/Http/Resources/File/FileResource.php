<?php

namespace App\Http\Resources\File;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class FileResource extends JsonResource
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
            'name'        => $this->name,
            'extension'   => $this->extension,
            'url'         => $this->url,
            'created_at'  => $this->created_at,
            'is_editable' => true,
        ];
    }
}
