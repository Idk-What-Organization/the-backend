<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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
        $token = $user->createToken('test-token')->plainTextToken;

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
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
        $token = $user->createToken('token')->plainTextToken;

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
        $token1 = $user->createToken('token1')->plainTextToken;
        $token2 = $user->createToken('token2')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
        ])->postJson('/api/logout')->assertStatus(200);

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'token2',
        ]);
    }
}
