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

class ActivitiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_see_his_comments_and_liked_posts(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $comment = Comment::factory()->create([
            'owner_id' => $user->id,
            'post_id'  => $post->id,
        ]);

        $this->actingAs($user);

        $post->like();

        $this->getJson('/api/activities')
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.liked_posts.0.id', $post->id)
                     ->where('data.comments.0.id', $comment->id)
                     ->where('data.comments.0.post_url', $post->path())
                     ->etc()
            );
    }
}
