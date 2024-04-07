<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class CommentLikesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_an_unauthenticated_user_cannot_like_comment(): void
    {
        $comment = Comment::factory()->create();

        $this->postJson("/api/comments/{$comment->id}/like")
             ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_an_unauthenticated_user_cannot_unlike_comment(): void
    {
        $comment = Comment::factory()->create();

        $this->deleteJson("/api/comments/{$comment->id}/like")
             ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_an_authenticated_user_can_like_comments(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create();

        $this->actingAs($user);

        $this->postJson("/api/comments/{$comment->id}/like")
             ->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseCount('likes', 1);
    }

    public function test_an_authenticated_user_can_unlike_their_liked_comments(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['owner_id' => $user]);

        $this->actingAs($user);

        $comment->like();

        $this->deleteJson("/api/comments/{$comment->id}/like")
             ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseCount('likes', 0);
    }

    public function test_an_authenticated_users_cannot_like_their_comments(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user);

        $this->postJson("/api/comments/{$comment->id}/like")
             ->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseCount('likes', 0);
    }

    public function test_an_authenticated_users_cannot_unlike_non_their_liked_comment(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create();

        $this->actingAs($user);

        $comment->like();

        $this->deleteJson("/api/comments/{$comment->id}/like")
             ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseCount('likes', 1);
    }
}
