<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository extends Abstracts\ABaseRepository implements Contracts\TaskRepositoryContract
{
    /**
     * @inheritDoc
     */
    function model () : string
    {
        return Task::class;
    }
}
