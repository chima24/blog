<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostLikesController extends Controller
{

    /**
     * @param Post $post
     * @return JsonResponse
     */
    public function store(Post $post): JsonResponse
    {
        $post->like();

        return response()->json(null, Response::HTTP_CREATED);
    }

    /**
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Post $post): JsonResponse
    {
        $post->unlike();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
