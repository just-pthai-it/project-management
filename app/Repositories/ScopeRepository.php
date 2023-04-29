<?php

namespace App\Repositories;

use App\Models\Scope;

class ScopeRepository extends Abstracts\ABaseRepository implements Contracts\ScopeRepositoryContract
{
    /**
     * @inheritDoc
     */
    function model () : string
    {
        return Scope::class;
    }
}
