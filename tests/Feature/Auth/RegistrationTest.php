<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * Skenario 1: Tes Pendaftaran Berhasil (Happy Path)
     */
    #[Test]
    public function it_should_register_a_user_successfully_and_return_a_token(): void
    {
        $userData = [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $expectedValidatedData = Arr::except($userData, ['password_confirmation']);

        $fakeUser = User::factory()->make(['email' => $userData['email']]);
        $fakeUser->id = 1;

        $fakeToken = 'dummy-jwt-token';

        $this->mock(AuthService::class, function (MockInterface $mock) use ($expectedValidatedData, $fakeUser, $fakeToken) {
            $mock->shouldReceive('register')
                ->once()
                ->with($expectedValidatedData)
                ->andReturn(['user' => $fakeUser, 'token' => $fakeToken]);
        });

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email'],
            'access_token',
            'token_type',
        ]);
        $response->assertJsonFragment([
            'message' => 'User successfully registered',
            'access_token' => $fakeToken,
            'email' => 'test@example.com'
        ]);
    }

    /**
     * @test
     * Skenario 2: Tes Gagal karena Validasi (Data tidak lengkap)
     */
    #[Test]
    public function it_should_return_a_validation_error_if_required_fields_are_missing(): void
    {
        $badUserData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ];

        $response = $this->postJson('/api/register', $badUserData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    /**
     * @test
     * Skenario 3: Gagal mendaftar karena email sudah digunakan
     */
    #[Test]
    public function it_should_return_a_validation_error_if_email_is_already_taken(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $userData = [
            'name' => 'New User',
            'username' => 'newuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * @test
     * Skenario 4: Gagal mendaftar karena password terlalu pendek
     */
    #[Test]
    public function it_should_return_a_validation_error_if_password_is_too_short(): void
    {
        $userData = [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    /**
     * @test
     * Skenario 5: Gagal mendaftar karena konfirmasi password tidak cocok
     */
    #[Test]
    public function it_should_return_a_validation_error_if_password_confirmation_does_not_match(): void
    {
        $userData = [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password_salah',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    /**
     * @test
     * Skenario 6: Gagal mendaftar karena format email tidak valid
     */
    #[Test]
    public function it_should_return_a_validation_error_if_email_format_is_invalid(): void
    {
        $userData = [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'ini-bukan-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
