<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{

    public function test_an_authenticated_user_can_change_his_own_name(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertNotEquals('NewName', $user->name);

        $response = $this->patchJson("/api/profile/change-name", [
            'name' => 'NewName',
        ]);

        $user->refresh();

        $this->assertEquals('NewName', $user->name);
    }

    public function test_an_authenticated_user_can_change_his_own_surname(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertNotEquals('surname', $user->surname);

        $response = $this->patchJson("/api/profile/change-surname", [
            'surname' => 'surname',
        ]);

        $user->refresh();

        $this->assertEquals('surname', $user->surname);
    }

    public function test_an_authenticated_user_can_change_his_own_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertFalse(Hash::check('password1', $user->password));

        $response = $this->patchJson("/api/profile/change-password", [
            'current_password'          => 'password',
            'new_password'              => 'password1',
            'new_password_confirmation' => 'password1'
        ]);

        $user->refresh();

        $this->assertTrue(Hash::check('password1', $user->password));
    }
}
