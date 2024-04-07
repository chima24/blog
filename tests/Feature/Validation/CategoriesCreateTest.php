<?php

namespace Tests\Feature\Validation;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CategoriesCreateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_category_must_contains_name()
    {
        $this->postJson('/api/categories', [])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->has('errors.name')
             );

        $this->postJson('/api/categories', ['name' => 'abc'])
             ->assertStatus(Response::HTTP_CREATED);
    }

    public function test_category_must_contains_name_at_least_3_symbols()
    {
        $name = __('validation.attributes.name');

        $this->postJson('/api/categories', ['name' => 'ab'])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
                 $json->has('message')
                      ->where(
                          'errors.name.0',
                          __('validation.min.string', ['attribute' => $name, 'min' => '3'])
                      )
             );

        $this->postJson('/api/categories', ['name' => 'abc'])
            ->assertStatus(Response::HTTP_CREATED);
    }
}
