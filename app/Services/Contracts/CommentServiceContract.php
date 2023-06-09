<?php

namespace App\Services\Contracts;

use App\Models\Comment;

interface CommentServiceContract
{
    public function list (array $inputs = []);

    public function listReplies (Comment $comment);

    public function get (Comment $comment);

    public function store (array $inputs);

    public function update (Comment $comment, array $inputs);

    public function delete (Comment $comment);
}
