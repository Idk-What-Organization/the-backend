<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Skenario 1: Login berhasil menggunakan email dan password yang benar
     */
    #[Test]
    public function it_can_log_in_a_user_with_correct_email_and_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $payload = [
            'identity' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'user', 'access_token', 'token_type'])
            ->assertJsonPath('user.id', $user->id);
    }

    /**
     * Skenario 2: Login berhasil menggunakan username dan password yang benar
     */
    #[Test]
    public function it_can_log_in_a_user_with_correct_username_and_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $payload = [
            'identity' => $user->username,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('user.username', $user->username);
    }

    /**
     * Skenario 3: Gagal login karena password salah
     */
    #[Test]
    public function it_returns_error_for_incorrect_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $payload = [
            'identity' => $user->email,
            'password' => 'password_salah',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('identity');
    }

    /**
     * Skenario 4: Gagal login karena user tidak ditemukan
     */
    #[Test]
    public function it_returns_error_for_non_existent_user(): void
    {
        $payload = [
            'identity' => 'tidak_ada@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('identity');
    }

    /**
     * Skenario 5: Gagal login karena field password tidak diisi
     */
    #[Test]
    public function it_returns_validation_error_if_a_field_is_missing(): void
    {
        $payload = ['identity' => 'testuser'];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }
}
