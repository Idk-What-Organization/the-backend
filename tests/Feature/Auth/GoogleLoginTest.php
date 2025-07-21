<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * Skenario 1: Callback Google berhasil menangani user baru dan membuat akun
     */
    #[Test]
    public function it_can_handle_google_callback_and_create_a_new_user(): void
    {
        $fakeGoogleUser = Mockery::mock(SocialiteUserContract::class);
        $fakeGoogleUser->shouldReceive('getId')->andReturn('123456789');
        $fakeGoogleUser->shouldReceive('getName')->andReturn('Google User');
        $fakeGoogleUser->shouldReceive('getEmail')->andReturn('googleuser@example.com');

        Socialite::shouldReceive('driver->user')->andReturn($fakeGoogleUser);

        $response = $this->get('/api/auth/google/callback');

        $this->assertDatabaseHas('users', [
            'email' => 'googleuser@example.com',
            'google_id' => '123456789',
        ]);

        $response->assertStatus(302);
        $response->assertRedirectContains('http://localhost:3000/login-success?token=');
    }

    /**
     * @test
     * Skenario 2: Callback Google berhasil login user yang sudah ada dan update google_id
     */
    #[Test]
    public function it_can_handle_google_callback_and_log_in_an_existing_user(): void
    {
        $existingUser = User::factory()->create(['email' => 'googleuser@example.com']);

        $fakeGoogleUser = Mockery::mock(SocialiteUserContract::class);
        $fakeGoogleUser->shouldReceive('getId')->andReturn('987654321');
        $fakeGoogleUser->shouldReceive('getName')->andReturn('Google User');
        $fakeGoogleUser->shouldReceive('getEmail')->andReturn('googleuser@example.com');

        Socialite::shouldReceive('driver->user')->andReturn($fakeGoogleUser);

        $response = $this->get('/api/auth/google/callback');

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'googleuser@example.com',
            'google_id' => '987654321'
        ]);

        $response->assertStatus(302);
        $response->assertRedirectContains('http://localhost:3000/login-success?token=');
    }
}
