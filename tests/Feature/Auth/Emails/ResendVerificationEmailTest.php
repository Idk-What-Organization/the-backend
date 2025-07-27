<?php

namespace Auth\Emails;

use App\Models\User;
use App\Notifications\VerifyEmailWithResend;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ResendVerificationEmailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        Queue::fake();
        config(['queue.default' => 'sync']);
        date_default_timezone_set('UTC');
    }

    /**
     * Tear down the test environment.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow(null);
    }

    /**
     * Test that a user can successfully resend a verification email.
     *
     * @return void
     */
    #[Test]
    public function user_can_resend_verification_email_successfully(): void
    {
        $user = User::factory()->unverified()->create();
        $response = $this->postJson('/api/email/resend-verification', ['email' => $user->email]);
        $response->assertOk()
            ->assertJson(['message' => 'Email verifikasi telah berhasil dikirim ulang.']);

        Notification::assertSentTo($user, VerifyEmailWithResend::class);

        $this->assertNotNull($user->fresh()->last_verification_email_sent_at);
    }

    /**
     * Test that a user cannot resend a verification email if already verified.
     *
     * @return void
     */
    #[Test]
    public function user_cannot_resend_verification_email_if_already_verified(): void
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/email/resend-verification', ['email' => $user->email]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email' => 'Email sudah diverifikasi.']);

        Notification::assertNotSentTo($user, VerifyEmailWithResend::class);
    }

    /**
     * Test that a user cannot resend a verification email too frequently.
     *
     * @return void
     */
    #[Test]
    public function user_cannot_resend_verification_email_too_frequently(): void
    {
        $user = User::factory()->unverified()->create();
        $this->postJson('/api/email/resend-verification', ['email' => $user->email])->assertOk();

        Notification::assertSentTo($user, VerifyEmailWithResend::class);
        Carbon::setTestNow(now()->addSeconds(59));

        $response = $this->postJson('/api/email/resend-verification', ['email' => $user->email]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        Notification::assertSentTimes(VerifyEmailWithResend::class, 1);
    }

    /**
     * Test that a user can resend a verification email after the cooldown period.
     *
     * @return void
     */
    #[Test]
    public function user_can_resend_verification_email_after_cooldown_period(): void
    {
        $user = User::factory()->unverified()->create();
        $this->postJson('/api/email/resend-verification', ['email' => $user->email])->assertOk();

        Notification::assertSentTimes(VerifyEmailWithResend::class, 1);

        $user->refresh();
        $this->travel(61)->seconds();
        $this->postJson('/api/email/resend-verification', ['email' => $user->email])->assertOk();

        Notification::assertSentTimes(VerifyEmailWithResend::class, 2);
    }

    /**
     * Test that a user cannot resend a verification email for a non-existent email.
     *
     * @return void
     */
    #[Test]
    public function user_cannot_resend_verification_email_for_non_existent_email(): void
    {
        $response = $this->postJson('/api/email/resend-verification', ['email' => 'nonexistent@example.com']);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email' => 'The selected email is invalid.']);

        Notification::assertNothingSent();
    }

    /**
     * Test that resending a verification email requires a valid email format.
     *
     * @return void
     */
    #[Test]
    public function resend_verification_email_requires_valid_email(): void
    {
        $response = $this->postJson('/api/email/resend-verification', ['email' => 'invalid-email']);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        Notification::assertNothingSent();
    }
}
