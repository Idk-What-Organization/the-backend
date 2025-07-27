<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an authenticated user can log out successfully.
     *
     * @return void
     */
    #[Test]
    public function an_authenticated_user_can_log_out(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);

        // Verify that the token is invalidated (cannot be used again)
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user')->assertStatus(401);
    }

    /**
     * Test that unauthenticated users cannot access the logout endpoint.
     *
     * @return void
     */
    #[Test]
    public function an_unauthenticated_user_cannot_log_out(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }

    /**
     * Test that a user with an invalid token gets unauthorized.
     *
     * @return void
     */
    #[Test]
    public function user_with_invalid_token_gets_unauthorized(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer InvalidToken123',
        ])->postJson('/api/logout');

        $response->assertStatus(401);
    }

    /**
     * Test that a user cannot log out twice using the same token.
     *
     * @return void
     */
    #[Test]
    public function user_cannot_logout_twice_with_same_token(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout')->assertStatus(200);

        $this->refreshApplication();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout')->assertStatus(401);
    }

    /**
     * Test that logout only deletes the current token and leaves others intact.
     *
     * @return void
     */
    #[Test]
    public function logout_only_deletes_current_token(): void
    {
        $user = User::factory()->create();
        $token1 = JWTAuth::fromUser($user);
        $token2 = JWTAuth::fromUser($user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
        ])->postJson('/api/logout')->assertStatus(200);

        // Verify that token1 is invalidated
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
        ])->postJson('/api/user')->assertStatus(401);

        // Verify that token2 is still valid (if JWTAuth allows multiple active tokens, which it does by default)
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token2,
        ])->postJson('/api/user')->assertStatus(200);
    }
}
