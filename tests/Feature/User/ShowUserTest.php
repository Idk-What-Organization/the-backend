<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShowUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * SCENARIO 1: USER FOUND
     * This test ensures a 200-OK response is returned along with the correct user data.
     *
     * @return void
     */
    #[Test]
    public function test_it_returns_user_profile_resource_when_user_is_found(): void
    {
        $user = User::factory()->create([
            'username' => 'johndoe',
        ]);

        $response = $this->getJson(route('api.users.show', ['username' => 'johndoe']));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'username',
                'bio',
                'joined_at',
                'photos' => [
                    'profile',
                    'cover',
                ],
                'stats' => [
                    'posts_count',
                    'friends_count',
                ],
            ],
        ]);

        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'username' => 'johndoe',
            ],
        ]);
    }

    /**
     * SCENARIO 2: USER NOT FOUND
     * This test ensures a 404 Not Found response is returned with the correct error message.
     *
     * @return void
     */
    #[Test]
    public function test_it_returns_a_404_not_found_error_when_user_does_not_exist(): void
    {
        $response = $this->getJson(route('api.users.show', ['username' => 'nonexistentuser']));
        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'User not found',
        ]);
    }
}
