<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_user_can_create_category(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertDatabaseCount('categories', 0);

        $response = $this->postJson('/api/categories', [
            'name'     => 'category',
            'owner_id' => $user->id,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseCount('categories', 1);
    }
}
