<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\VerifyEmailWithResend;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Notification;

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
        Notification::fake();

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['message'])
            ->assertJsonFragment([
                'message' => 'Registration successful. Please check your email to verify your account.',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
            'email_verified_at' => null,
        ]);

        $user = User::where('email', $payload['email'])->first();

        // UBAH BARIS INI
        Notification::assertSentTo(
            $user,
            VerifyEmailWithResend::class // Gunakan kelas notifikasi yang benar
        );
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
                ->with($validatedData, Mockery::any())
                ->andReturn(User::factory()->make($validatedData));
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

    /**
     * email_verification: Should successfully verify user email.
     *
     * @return void
     */
    #[Test]
    public function email_verification_can_verify_user_email(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Email verified successfully!', 'token_type' => 'Bearer']);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    /**
     * email_verification: Should not verify email with invalid hash.
     *
     * @return void
     */
    #[Test]
    public function email_verification_cannot_verify_with_invalid_hash(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'invalid-hash']
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(400);
        $this->assertNull($user->fresh()->email_verified_at);
    }

    /**
     * email_verification: Should not verify email with expired signature.
     *
     * @return void
     */
    #[Test]
    public function email_verification_cannot_verify_with_expired_signature(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // Create an expired URL
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinutes(5),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(403);
        $this->assertNull($user->fresh()->email_verified_at);
    }

    /**
     * email_verification: Should resend verification email.
     *
     * @return void
     */
    #[Test]
    public function email_verification_can_resend_verification_email(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->postJson('/api/email/verification-notification');

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Verification email sent.']);

        \Illuminate\Support\Facades\Mail::assertSent(\Illuminate\Auth\Notifications\VerifyEmail::class);
    }

    /**
     * email_verification: Should not resend verification email if already verified.
     *
     * @return void
     */
    #[Test]
    public function email_verification_cannot_resend_if_already_verified(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson('/api/email/verification-notification');

        $response->assertStatus(400)
            ->assertJsonFragment(['message' => 'Email already verified.']);

        \Illuminate\Support\Facades\Mail::assertNotSent(\Illuminate\Auth\Notifications\VerifyEmail::class);
    }

    /**
     * email_verification: Should apply rate limiting to resend verification email.
     *
     * @return void
     */
    #[Test]
    public function email_verification_applies_rate_limiting_to_resend_email(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // Send multiple requests to trigger rate limit
        for ($i = 0; $i < 7; $i++) {
            $this->actingAs($user)->postJson('/api/email/verification-notification');
        }

        $response = $this->actingAs($user)->postJson('/api/email/verification-notification');

        $response->assertStatus(429)
            ->assertJsonFragment(['message' => 'Too Many Attempts.']);

        // Only 6 emails should have been sent due to rate limiting
        \Illuminate\Support\Facades\Mail::assertSent(\Illuminate\Auth\Notifications\VerifyEmail::class, 6);
    }

    /**
     * verified_middleware: Should allow access to verified routes for verified users.
     *
     * @return void
     */
    #[Test]
    public function verified_middleware_allows_access_for_verified_users(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Define a temporary route protected by 'verified' middleware
        \Illuminate\Support\Facades\Route::middleware(['auth:api', 'verified'])->get('/test-verified', function () {
            return response()->json(['message' => 'Access granted.']);
        });

        $response = $this->actingAs($user)->getJson('/test-verified');

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Access granted.']);
    }

    /**
     * verified_middleware: Should deny access to verified routes for unverified users.
     *
     * @return void
     */
    #[Test]
    public function verified_middleware_denies_access_for_unverified_users(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // Define a temporary route protected by 'verified' middleware
        \Illuminate\Support\Facades\Route::middleware(['auth:api', 'verified'])->get('/test-verified', function () {
            return response()->json(['message' => 'Access granted.']);
        });

        $response = $this->actingAs($user)->getJson('/test-verified');

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'Your email address is not verified.']);
    }
}
