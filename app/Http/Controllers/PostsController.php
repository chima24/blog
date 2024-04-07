<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PostsController extends Controller
{
    public const PAGINATION_COUNT = 10;

    /**
     * Display a listing of the resource.
     *
     * @return PostCollection
     */
    public function index(): PostCollection
    {
        $posts = Post::latest()->paginate(self::PAGINATION_COUNT);

        return new PostCollection($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PostCreateRequest $request
     * @return JsonResponse
     */
    public function store(PostCreateRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $validatedData = $request->validated();

        auth()->user()->posts()->create($validatedData);

        return response()->json(null, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return PostResource
     */
    public function show(Post $post): PostResource
    {
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PostUpdateRequest $request
     * @param Post $post
     * @return JsonResponse
     */
    public function update(PostUpdateRequest $request, Post $post): JsonResponse
    {
        $validatedData = $request->validated();

        $post->update($validatedData);

        return response()->json(null, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);

    }
}
