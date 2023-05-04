<?php

namespace App\Repositories;

use App\Models\Comment;

class CommentRepository extends Abstracts\ABaseRepository implements Contracts\CommentRepositoryContract
{
    /**
     * @inheritDoc
     */
    function model () : string
    {
        return Comment::class;
    }
}
