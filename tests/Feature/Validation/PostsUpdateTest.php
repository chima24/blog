<?php
declare(strict_types=1);

namespace Tests\Feature\Validation;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PostsUpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public Category $category;
    public Post $post;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();

        $this->category = Category::factory()->create();
        $this->post = Post::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user);
    }

    public function test_a_post_must_contains_title()
    {
        $this->patchJson("api/posts/{$this->post->slug}", [
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->has('errors.title')
             );

        $this->patchJson("api/posts/{$this->post->slug}", [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_OK);
    }

    public function test_a_post_must_contains_title_at_least_6_symbols()
    {
        $title = __('validation.attributes.title');

        $this->patchJson("api/posts/{$this->post->slug}", [
            'title'       => $this->faker->lexify('?????'),
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->where(
                      'errors.title.0',
                      __('validation.min.string', ['attribute' => $title, 'min' => '6'])
                  )
             );

        $this->patchJson("api/posts/{$this->post->slug}", [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_OK);
    }

    public function test_a_post_must_contains_body()
    {
        $this->patchJson("api/posts/{$this->post->slug}", [
            'title'       => $this->faker->lexify('??????'),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->has('errors.body')
             );

        $this->patchJson("api/posts/{$this->post->slug}", [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_OK);
    }

    public function test_a_post_must_contains_body_at_least_150_symbols()
    {
        $body = __('validation.attributes.body');

        $this->patchJson("api/posts/{$this->post->slug}", [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(3),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->where(
                      'errors.body.0',
                      __('validation.min.string', ['attribute' => $body, 'min' => '150'])
                  )
             );

        $this->patchJson("api/posts/{$this->post->slug}", [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_OK);

    }

    public function test_a_post_must_contains_category_id()
    {
        $this->patchJson("api/posts/{$this->post->slug}", [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
        ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->has('errors.category_id')
             );

        $this->patchJson("api/posts/{$this->post->slug}", [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_OK);
    }

    public function test_a_post_must_contains_category_that_exists()
    {
        $categoryId = __('validation.attributes.category_id');

        $this->patchJson("api/posts/{$this->post->slug}", [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
            'category_id' => 2, // non-existing category
        ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->where(
                      'errors.category_id.0',
                      __('validation.exists', ['attribute' => $categoryId, 'min' => '150'])
                  )
             );

        $this->patchJson("api/posts/{$this->post->slug}", [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_OK);
    }
}
