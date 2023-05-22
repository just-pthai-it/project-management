<?php

namespace App\Http\Resources\Role;

use App\Http\Resources\Permission\PermissionCollection;
use App\Models\Role;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class RoleResource extends JsonResource
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
            'is_editable' => $this->name != Role::ROLE_ROOT_NAME,
            'permissions' => new PermissionCollection($this->permissions),
        ];
    }
}
