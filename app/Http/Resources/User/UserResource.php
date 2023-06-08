<?php

namespace App\Http\Resources\User;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class UserResource extends JsonResource
{
    private bool $isIncludePermissions = false;

    /**
     * @param bool $isIncludePermissions
     */
    public function setIsIncludePermissions (bool $isIncludePermissions) : void
    {
        $this->isIncludePermissions = $isIncludePermissions;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray ($request) : array|JsonSerializable|Arrayable
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->whenHas('email'),
            'phone'         => $this->whenHas('phone'),
            'address'       => $this->whenHas('address'),
            'date_of_birth' => $this->whenHas('date_of_birth'),
            'job_title'     => $this->whenHas('job_title'),
            'status'        => $this->whenHas('status'),
            'avatar'        => $this->whenHas('avatar'),
            'roles'         => $this->whenLoaded('roles'),
            'permissions'   => $this->when($this->isIncludePermissions, fn () => $this->permissions),
            'is_root'       => $this->isRoot(),
            'is_editable' => $this->is_editable,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }

}
