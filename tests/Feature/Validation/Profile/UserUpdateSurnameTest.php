<?php

namespace Tests\Feature\Validation\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserUpdateSurnameTest extends TestCase
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
        $this->patchJson('/api/profile/change-surname', [])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->has('errors.surname')
             );

        $this->patchJson('/api/profile/change-surname', ['surname' => 'abc'])
             ->assertStatus(Response::HTTP_OK);
    }

    public function test_a_user_must_provide_name_at_least_3_letters()
    {
        $surname = __('validation.attributes.surname');

        $this->patchJson('/api/profile/change-surname', ['surname' => 'ab'])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->where(
                      'errors.surname.0',
                      __('validation.min.string', ['attribute' => $surname, 'min' => '3'])
                  )
             );

        $this->patchJson('/api/profile/change-surname', ['surname' => 'abc'])
             ->assertStatus(Response::HTTP_OK);
    }
}
