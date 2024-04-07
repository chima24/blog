<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class PostLikesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_an_unauthenticated_user_cannot_like_post(): void
    {
        $post = Post::factory()->create();

        $this->postJson("/api/posts/{$post->slug}/like")
             ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_an_unauthenticated_user_cannot_unlike_post(): void
    {
        $post = Post::factory()->create();

        $this->deleteJson("/api/posts/{$post->slug}/like")
             ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_an_authenticated_user_can_like_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user);

        $this->postJson("/api/posts/{$post->slug}/like")
             ->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseCount('likes', 1);
    }

    public function test_an_authenticated_user_can_unlike_their_liked_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['owner_id' => $user]);

        $this->actingAs($user);

        $post->like();

        $this->deleteJson("/api/posts/{$post->slug}/like")
             ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseCount('likes', 0);
    }

    public function test_an_authenticated_users_cannot_like_their_posts(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user);

        $this->postJson("/api/posts/{$post->slug}/like")
             ->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseCount('likes', 0);
    }

    public function test_an_authenticated_users_cannot_unlike_non_their_liked_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user);

        $post->like();

        $this->deleteJson("/api/posts/{$post->slug}/like")
             ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseCount('likes', 1);
    }
}
