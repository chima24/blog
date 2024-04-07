<?php

namespace Tests\Feature\Validation\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserUpdateNameTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_a_user_must_provide_name()
    {
        $this->patchJson('/api/profile/change-name', [])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->has('errors.name')
             );

        $this->patchJson('/api/profile/change-name', ['name' => 'abc'])
             ->assertStatus(Response::HTTP_OK);
    }

    public function test_a_user_must_provide_name_at_least_3_letters()
    {
        $name = __('validation.attributes.name');

        $this->patchJson('/api/profile/change-name', ['name' => 'ab'])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->where(
                      'errors.name.0',
                      __('validation.min.string', ['attribute' => $name, 'min' => '3'])
                  )
             );

        $this->patchJson('/api/profile/change-name', ['name' => 'abc'])
             ->assertStatus(Response::HTTP_OK);
    }
}
