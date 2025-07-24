<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Mockery;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Generate valid registration data for testing.
     *
     * @param array $overrides Fields to override in the default valid data.
     * @return array
     */
    private function validData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@mail.com',
            'password' => 'P@ssw0rd123!',
            'password_confirmation' => 'P@ssw0rd123!',
        ], $overrides);
    }

    /**
     * happy_path: Should register a user successfully and return an access token.
     *
     * @return void
     */
    #[Test]
    public function happy_path_registers_user_and_returns_token(): void
    {
        $payload = $this->validData();
        $expectedValidatedData = Arr::except($payload, ['password_confirmation']);
        $fakeUser = User::factory()->make(['email' => $payload['email']]);
        $fakeUser->id = 1;
        $fakeToken = 'dummy-jwt-token';

        $this->mock(AuthService::class, function (MockInterface $mock) use ($expectedValidatedData, $fakeUser, $fakeToken) {
            $mock->shouldReceive('register')
                ->once()
                ->with($expectedValidatedData, Mockery::any()) // <-- PERBAIKAN DI SINI
                ->andReturn(['user' => $fakeUser, 'token' => $fakeToken]);
        });

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'access_token',
                'token_type',
            ])
            ->assertJsonFragment(['access_token' => $fakeToken]);
    }

    /**
     * security: Should ignore unexpected fields to prevent mass assignment vulnerability.
     *
     * @return void
     */
    #[Test]
    public function security_ignores_extra_fields_during_registration(): void
    {
        $payload = $this->validData(['role' => 'admin', 'is_admin' => true]);

        $this->mock(AuthService::class, function (MockInterface $mock) use ($payload) {
            $validatedData = Arr::except($payload, ['role', 'is_admin', 'password_confirmation']);

            $mock->shouldReceive('register')
                ->once()
                ->with($validatedData, Mockery::any()) // <-- PERBAIKIKAN DI SINI
                ->andReturn([
                    'user' => User::factory()->make($validatedData),
                    'token' => 'dummy-token'
                ]);
        });

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201);
    }

    /**
     * Provides various invalid registration payloads with the expected error field.
     *
     * @return array<string, array{0: array, 1: string}>
     */
    public static function invalidDataProvider(): array
    {
        $createCase = function ($field, $value, $name) {
            $valid = [
                'name' => 'Test User',
                'username' => 'testuser',
                'email' => 'test@mail.com',
                'password' => 'P@ssw0rd123!',
                'password_confirmation' => 'P@ssw0rd123!',
            ];

            $payload = array_merge($valid, is_array($field) ? $field : [$field => $value]);

            if (is_string($field) && $field === 'password') {
                $payload['password_confirmation'] = $value;
            }

            return [$name => [$payload, is_array($field) ? $value : $field]];
        };

        return array_merge(
            $createCase('email', 'sudahada@mail.com', 'email already taken'),
            $createCase('password', '123', 'password too short'),
            $createCase('password', 'salahkonfirmasi', 'password confirmation mismatch'),
            $createCase('email', 'ini-bukan-email', 'invalid email format'),
            $createCase('name', 'User <invalid> 123', 'invalid name characters'),
            $createCase('username', 'user with space', 'invalid username characters'),
            $createCase('password', 'password12345', 'password lacks complexity'),
            ['missing required fields' => [['name' => 'Test User', 'email' => 'test@mail.com'], 'password']],
            $createCase('username', 'usertaken', 'username already taken'),
            $createCase('email', 'TEST@mail.com', 'email with uppercase is still unique'),
            $createCase('name', '', 'empty string for required field'),
            $createCase('name', null, 'null for required field'),
            ['entirely empty payload' => [[], 'name']],
        );
    }

    /**
     * validation_error: Should return proper validation errors for invalid inputs.
     *
     * @param array $payload The invalid request data.
     * @param string $errorField The field expected to trigger a validation error.
     * @return void
     */
    #[Test]
    #[DataProvider('invalidDataProvider')]
    public function validation_error_returns_proper_messages(array $payload, string $errorField): void
    {
        if (isset($payload['email']) && $payload['email'] === 'sudahada@mail.com') {
            User::factory()->create(['email' => 'sudahada@mail.com']);
        }

        if (isset($payload['username']) && $payload['username'] === 'usertaken') {
            User::factory()->create(['username' => 'usertaken', 'email' => 'unique-email@mail.com']);
        }

        if (isset($payload['email']) && strtolower($payload['email']) === 'test@mail.com') {
            User::factory()->create(['email' => 'test@mail.com']);
        }

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure(['success', 'message', 'errors'])
            ->assertJsonValidationErrors([$errorField]);
    }
}
