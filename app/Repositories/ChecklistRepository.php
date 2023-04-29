<?php

namespace App\Repositories;

use App\Models\Checklist;

class ChecklistRepository extends Abstracts\ABaseRepository implements Contracts\ChecklistRepositoryContract
{
    /**
     * @inheritDoc
     */
    function model () : string
    {
        return Checklist::class;
    }
}
