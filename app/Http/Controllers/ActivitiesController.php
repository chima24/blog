<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ActivitiesController extends Controller
{

    /**
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        $user = auth()->user();

        $likedPosts = Like::where(['user_id' => $user->id, 'likeable_type' => 'App\Models\Post'])->get();
        $comments = $this->getUserCommentsWithPostPath($user);

        return response()->json([
            'data' => [
                'liked_posts' => $likedPosts,
                'comments'    => $comments
            ]
        ], Response::HTTP_OK);
    }

    /**
     * @param User $user
     * @return Collection
     */
    protected function getUserCommentsWithPostPath(User $user): Collection
    {
        $comments = Comment::where('owner_id', $user->id)->with('post')->get();

        foreach ($comments as &$comment) {
            $comment['post_url'] = $comment->post->path();
            unset($comment['post']);
        }

        return $comments;
    }
}
