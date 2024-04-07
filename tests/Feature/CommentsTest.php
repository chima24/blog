<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_an_unauthenticated_user_cannot_comment_post(): void
    {
        $post = Post::factory()->create();

        $this->postJson("/api/posts/{$post->slug}/comments", [
            'body' => $this->faker->sentence(15),
        ])->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_an_authenticated_user_can_comment_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user);

        $this->assertDatabaseCount('comments', 0);

        $this->postJson("/api/posts/{$post->slug}/comments", [
            'body' => $this->faker->sentence(15),
            'owner_id' => $user->id,
        ])->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseCount('comments', 1);
    }

    public function test_an_unauthenticated_user_cannot_delete_comment(): void
    {
        $comment = Comment::factory()->create();

        $this->deleteJson("/api/comments/{$comment->id}")
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_an_authenticated_user_cannot_delete_other_user_comment(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $comment = Comment::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user2);

        $this->deleteJson("/api/comments/{$comment->id}")
             ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_an_authenticated_user_can_delete_his_own_comment(): void
    {
        $user = User::factory()->create();

        $comment = Comment::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user);

        $this->assertDatabaseCount('comments', 1);

        $this->deleteJson("/api/comments/{$comment->id}")
             ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_all_users_can_see_comments_on_post(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $this->getJson("/api/posts/{$post->slug}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.comments.0.id', $comment->id)
                     ->where('data.comments.0.post_id', $post->id)
                     ->where('data.comments.0.owner_id', $comment->owner->id)
                     ->where('data.comments.0.body', $comment->body)
                     ->etc()
            );
    }
}
