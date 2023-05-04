<?php

namespace App\Services;

use App\Helpers\CusResponse;
use App\Models\Comment;
use App\Repositories\Contracts\CommentRepositoryContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class CommentService implements Contracts\CommentServiceContract
{
    private CommentRepositoryContract $commentRepository;

    public function __construct (CommentRepositoryContract $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function list (array $inputs = []) {}

    public function get (Comment $comment) {}

    public function store (array $inputs) {}

    public function update (Comment $comment, array $inputs) : JsonResponse
    {
        $comment->update($inputs);
        return CusResponse::successfulWithNoData();
    }

    public function delete (Comment $comment) : JsonResponse
    {
        $comment->comments()
                ->update(Arr::only($comment->getOriginal(), ['commentable_type', 'commentable_id', 'deep_level']));
        $comment->delete();
        return CusResponse::successfulWithNoData();
    }
}
