<?php

namespace App\Http\Resources\User;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class UserResource extends JsonResource
{
    private bool $isIncludePermissions;

    public function __construct ($resource, bool $isIncludePermissions = false)
    {
        parent::__construct($resource);
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
            'email'         => $this->email,
            'phone'         => $this->phone,
            'address'       => $this->address,
            'date_of_birth' => $this->date_of_birth,
            'job_title'     => $this->job_title,
            'status'        => $this->status,
            'avatar'        => $this->avatar,
            'roles'         => $this->roles,
            'permissions'   => $this->when($this->isIncludePermissions, $this->permissions),
        ];
    }

}
