<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Register test
     *
     * @return void
     */
    public function test_a_user_can_register_to_the_application(): void
    {
        $response = $this->postJson('/register', [
            'username'              => 'username',
            'password'              => 'password',
            'password_confirmation' => 'password',
            'name'                  => 'name',
            'surname'               => 'surname'
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_a_user_can_login_into_the_application(): void
    {
        $user = User::factory()->create(['username' => 'username']);

        $response = $this->postJson('/login', [
            'username' => $user->username,
            'password' => 'password'
        ]);

        $response
            ->assertStatus(Response::HTTP_NO_CONTENT)
            ->assertSessionHas('auth');
    }

    public function test_a_user_can_logout_from_the_application()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/login', [
            'username' => $user->username,
            'password' => 'password'
        ]);

        $response
            ->assertStatus(Response::HTTP_NO_CONTENT)
            ->assertSessionHas('auth');

        $response = $this->postJson('/logout');

        $response->assertStatus(Response::HTTP_NO_CONTENT)
                 ->assertSessionMissing('auth');
    }
}
