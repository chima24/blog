<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PostsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_all_users_can_see_posts_and_pagination(): void
    {
        $posts = Post::factory(20)->create();

        $response = $this->getJson('/api/posts')
                         ->assertStatus(Response::HTTP_OK)
                         ->assertJsonStructure(
                             [
                                 'data'  => [
                                     [
                                         'id',
                                         'title',
                                         'body',
                                         'comments_count',
                                         'likes_count'
                                     ]
                                 ],
                                 'links' => [
                                 ],
                                 'meta'  => [
                                 ]
                             ]
                         );
    }

    public function test_an_authenticated_user_can_create_post(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/api/posts', [
            'title'       => $this->faker->sentence(10),
            'category_id' => $category->id,
            'owner_id'    => $user->id,
            'body'        => $this->faker->sentence(200),
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseCount('posts', 1);
    }

    public function test_an_non_owner_of_post_cannot_edit_it(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user2);

        $response = $this->patchJson("/api/posts/{$post->slug}", [
            'title'       => 'My New Title',
            'body'        => $this->faker->sentence(200),
            'category_id' => $post->category_id,
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_an_owner_of_post_can_edit_it(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->patchJson("/api/posts/{$post->slug}", [
            'title'       => 'My New Title',
            'body'        => $this->faker->sentence(200),
            'category_id' => $post->category_id,
        ]);

        $this->assertNotEquals('My New Title', $post->title);
        $post->refresh();
        $this->assertEquals('My New Title', $post->title);
    }

    public function test_an_non_owner_of_post_cannot_delete_it(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user2);

        $this->deleteJson("/api/posts/{$post->slug}")
             ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_an_owner_of_post_can_delete_it(): void
    {
        $user = User::factory()->create();

        $post = Post::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user);

        $this->assertDatabaseHas('posts', ['title' => $post->title]);

        $this->deleteJson("/api/posts/{$post->slug}")
             ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('posts', ['title' => $post->title]);
    }

    public function test_all_users_can_view_specific_blog_post(): void
    {
        $post = Post::factory()->create();

        $this->getJson("/api/posts/{$post->slug}")
            ->assertStatus(Response::HTTP_OK)
             ->assertExactJson(
                 [
                     'data' => [
                         'owner_name'    => $post->owner->name,
                         'owner_surname' => $post->owner->surname,
                         'title'         => $post->title,
                         'body'          => $post->body,
                         'comments'      => [],
                     ]
                 ]
             );
    }

}
