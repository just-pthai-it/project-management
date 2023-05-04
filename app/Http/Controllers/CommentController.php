<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\UpdateCommentPatchRequest;
use App\Models\Comment;
use App\Services\Contracts\CommentServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    private CommentServiceContract $commentService;

    /**
     * @param CommentServiceContract $commentService
     */
    public function __construct (CommentServiceContract $commentService)
    {
        $this->authorizeResource(Comment::class, 'comment');
        $this->commentService = $commentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index ()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function store (Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show ($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCommentPatchRequest $request
     * @param Comment                   $comment
     * @return JsonResponse
     */
    public function update (UpdateCommentPatchRequest $request, Comment $comment) : JsonResponse
    {
        return $this->commentService->update($comment, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Comment $comment
     * @return JsonResponse
     */
    public function destroy (Comment $comment) : JsonResponse
    {
        return $this->commentService->delete($comment);
    }

    public function listReplies (Comment $comment) : JsonResponse
    {
        return $this->commentService->listReplies($comment);
    }
}
