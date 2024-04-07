<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CommentCreateRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentsController extends Controller
{

    /**
     * @param CommentCreateRequest $request
     * @param Post $post
     * @return JsonResponse
     */
    public function store(CommentCreateRequest $request, Post $post): JsonResponse
    {
        $validatedData = $request->validated();

        $post->comments()->create($validatedData);

        return response()->json(null, Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Comment $comment
     * @return JsonResponse
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
