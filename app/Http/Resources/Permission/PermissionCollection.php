<?php

namespace App\Http\Resources\Permission;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class PermissionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray ($request) : array|JsonSerializable|Arrayable
    {
        $this->collection = $this->collection->groupBy('group_name');
        $this->collection->transform(function ($item, $key)
        {
            $item->transform(function ($item, $key)
            {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            });

            return [
                'groupName'   => $key,
                'permissions' => $item,
            ];
        });

        return $this->collection->values();
    }
}
