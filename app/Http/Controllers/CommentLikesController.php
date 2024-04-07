<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentLikesController extends Controller
{

    /**
     * @param Comment $comment
     * @return JsonResponse
     */
    public function store(Comment $comment): JsonResponse
    {
        $comment->like();

        return response()->json(null, Response::HTTP_CREATED);
    }

    /**
     * @param Comment $comment
     * @return JsonResponse
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $comment->unlike();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
