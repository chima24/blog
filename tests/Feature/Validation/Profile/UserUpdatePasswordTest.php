<?php

namespace Tests\Feature\Validation\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserUpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_a_user_must_provide_current_password_and_new_confirmed_password()
    {
        $this->patchJson('/api/profile/change-password', [])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->has('errors.current_password')
                  ->has('errors.new_password')
             );

        $this->patchJson('/api/profile/change-password', [
            'current_password' => 'password',
            'new_password' => 'password1',
        ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                  ->has('errors.new_password')
             );

        $this->patchJson('/api/profile/change-password', [
            'current_password' => 'password',
            'new_password' => 'password1',
            'new_password_confirmation' => 'password1',
        ])
             ->assertStatus(Response::HTTP_OK);
    }

    public function test_a_user_must_provide_current_password_and_new_confirmed_password_at_least_8_letters()
    {
        $this->patchJson('/api/profile/change-password', [
            'current_password' => 'pass',
            'new_password' => 'pass',
            'new_password_confirmation' => 'pass',
        ])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
             ->assertJson(fn (AssertableJson $json) =>
             $json->has('message')
                 ->has('errors.current_password')
                 ->has('errors.new_password')
             );

        $this->patchJson('/api/profile/change-password', [
            'current_password' => 'password',
            'new_password' => 'password1',
            'new_password_confirmation' => 'password1',
        ])
             ->assertStatus(Response::HTTP_OK);
    }
}
