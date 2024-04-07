<?php

namespace Tests\Feature\Validation;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CommentsCreateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public Post $post;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->post = Post::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user);
    }

    public function test_comment_must_contains_body()
    {
        $this->postJson("/api/posts/{$this->post->slug}/comments", [])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->has('errors.body')
             );

        $this->postJson("/api/posts/{$this->post->slug}/comments", ['body' => $this->faker->lexify('??????????')])
             ->assertStatus(Response::HTTP_CREATED);
    }

    public function test_comment_must_contains_body_at_least_10_symbols()
    {
        $body = __('validation.attributes.body');

        $this->postJson("/api/posts/{$this->post->slug}/comments", ['body' => $this->faker->lexify('????????')])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->where(
                      'errors.body.0',
                      __('validation.min.string', ['attribute' => $body, 'min' => '10'])
                  )
             );

        $this->postJson("/api/posts/{$this->post->slug}/comments", ['body' => $this->faker->lexify('??????????')])
             ->assertStatus(Response::HTTP_CREATED);
    }
}
