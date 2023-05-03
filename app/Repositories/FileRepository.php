<?php

namespace App\Repositories;

use App\Models\File;

class FileRepository extends Abstracts\ABaseRepository implements Contracts\FileRepositoryContract
{
    /**
     * @inheritDoc
     */
    function model () : string
    {
        return File::class;
    }
}
