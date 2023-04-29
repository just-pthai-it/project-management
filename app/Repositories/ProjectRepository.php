<?php

namespace App\Repositories;

use App\Models\Project;

class ProjectRepository extends Abstracts\ABaseRepository implements Contracts\ProjectRepositoryContract
{
    /**
     * @inheritDoc
     */
    function model () : string
    {
        return Project::class;
    }
}
