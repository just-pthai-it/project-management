<?php

namespace App\CommandBus\Actions\Project;

use App\Models\TaskStatus;
use Illuminate\Database\Eloquent\Builder;

class GenerateWithCountTasksByStatusQuery
{
    public function __invoke () : array
    {
        $withCountArr = [];
        foreach (TaskStatus::STATUSES as $id => $name)
        {
            $withCountArr["tasks as {$id}_tasks"] = function (Builder $query) use ($id)
            {
                $query->where('status_id', '=', $id);
            };
        }

        return $withCountArr;
    }
}