<?php

namespace Tests\Feature\Validation;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PostsCreateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public Category $category;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->category = Category::factory()->create();

        $this->actingAs($user);
    }

    public function test_a_post_must_contains_title()
    {
        $this->postJson('/api/posts', [
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->has('errors.title')
             );

        $this->postJson('/api/posts', [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_CREATED);
    }

    public function test_a_post_must_contains_title_at_least_6_symbols()
    {
        $title = __('validation.attributes.title');

        $this->postJson('/api/posts', [
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

        $this->postJson('/api/posts', [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
            ->assertStatus(Response::HTTP_CREATED);
    }

    public function test_a_post_must_contains_body()
    {
        $this->postJson('/api/posts', [
            'title'       => $this->faker->lexify('??????'),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->has('errors.body')
             );

        $this->postJson('/api/posts', [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_CREATED);
    }

    public function test_a_post_must_contains_body_at_least_150_symbols()
    {
        $body = __('validation.attributes.body');

        $this->postJson('/api/posts', [
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

        $this->postJson('/api/posts', [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_CREATED);

    }

    public function test_a_post_must_contains_category_id()
    {
        $this->postJson('/api/posts', [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
        ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->has('errors.category_id')
             );

        $this->postJson('/api/posts', [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_CREATED);
    }

    public function test_a_post_must_contains_category_that_exists()
    {
        $categoryId = __('validation.attributes.category_id');

        $this->postJson('/api/posts', [
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

        $this->postJson('/api/posts', [
            'title'       => $this->faker->lexify('??????'),
            'body'        => $this->faker->sentence(200),
            'category_id' => $this->category->id,
        ])
             ->assertStatus(Response::HTTP_CREATED);
    }
}
