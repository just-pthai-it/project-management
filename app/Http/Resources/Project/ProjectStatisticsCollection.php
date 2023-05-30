<?php

namespace App\Http\Resources\Project;

use App\Models\ProjectStatus;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class ProjectStatisticsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray ($request) : array|JsonSerializable|Arrayable
    {
        foreach (ProjectStatus::STATUSES as $id => $name)
        {
            if ($this->collection->doesntContain('status_id', $id))
            {
                $this->collection->push((object)['status_id' => $id, 'projects_count' => 0]);
            }
        }

        $projectsCount   = 0;
        $projectStatuses = ProjectStatus::all();
        $this->collection->transform(function ($groupProjectCount, $key) use ($projectStatuses, &$projectsCount)
        {
            $projectStatus = $projectStatuses->firstWhere('id', '=', $groupProjectCount->status_id);
            $projectsCount += $groupProjectCount->projects_count;

            return [
                'id'             => $projectStatus->id,
                'name'           => $projectStatus->name,
                'color'          => $projectStatus->color,
                'projects_count' => $groupProjectCount->projects_count,
            ];
        });

        return $this->collection;
    }
}
