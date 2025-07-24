<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RateLimiterTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test to ensure login attempts are blocked after too many failures.
     *
     * @return void
     */
    #[Test]
    public function it_blocks_login_attempts_after_too_many_failures(): void
    {
        $user = User::factory()->create();

        $payload = [
            'identity' => $user->email,
            'password' => 'password_salah',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', $payload)->assertStatus(422);
        }

        $this->postJson('/api/login', $payload)->assertStatus(429);
    }

    /**
     * Test to ensure attempts are blocked after reaching hourly limit.
     *
     * @return void
     */
    #[Test]
    public function it_blocks_attempts_based_on_the_hourly_limit(): void
    {
        $user = User::factory()->create();
        $payload = [
            'identity' => $user->email,
            'password' => 'password_salah',
        ];

        for ($round = 0; $round < 4; $round++) {
            for ($i = 0; $i < 5; $i++) {
                $this->postJson('/api/login', $payload)->assertStatus(422);
            }

            $this->postJson('/api/login', $payload)->assertStatus(429);

            $this->travel(1)->minute();
        }

        $this->postJson('/api/login', $payload)->assertStatus(429);
    }

    /**
     * Test to ensure rate limiter resets the counter after one minute.
     *
     * @return void
     */
    #[Test]
    public function it_resets_the_counter_after_one_minute(): void
    {
        $user = User::factory()->create();
        $payload = [
            'identity' => $user->email,
            'password' => 'password_salah',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', $payload)->assertStatus(422);
        }

        $this->postJson('/api/login', $payload)->assertStatus(429);

        $this->travel(1)->minute();

        $this->postJson('/api/login', $payload)->assertStatus(422);
    }

    /**
     * Test to ensure different users from the same IP are not blocked.
     *
     * @return void
     */
    #[Test]
    public function it_does_not_block_different_users_from_the_same_ip(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $payloadUserA = [
            'identity' => $userA->email,
            'password' => 'password_salah',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', $payloadUserA)->assertStatus(422);
        }

        $this->postJson('/api/login', $payloadUserA)->assertStatus(429);

        $payloadUserB = [
            'identity' => $userB->email,
            'password' => 'password_salah',
        ];

        $this->postJson('/api/login', $payloadUserB)->assertStatus(422);
    }
}
