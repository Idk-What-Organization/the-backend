<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShowUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Returns public profile when accessed by guest or other users.
     */
    #[Test]
    public function test_it_returns_public_profile_when_guest_or_other_user(): void
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

        $response->assertJsonMissing(['email']);
    }

    /**
     * Returns private profile when requested by the profile owner.
     */
    #[Test]
    public function test_it_returns_private_profile_when_owner_requests(): void
    {
        $user = User::factory()->create([
            'username' => 'johndoe',
            'email' => 'johndoe@example.com',
        ]);

        $response = $this->actingAs($user)->getJson(
            route('api.users.show', ['username' => 'johndoe'])
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'username',
                'email',
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
                'email' => 'johndoe@example.com',
            ],
        ]);
    }

    /**
     * Returns public profile when requested by a different authenticated user.
     */
    #[Test]
    public function it_returns_public_profile_when_requested_by_another_authenticated_user(): void
    {
        $profileUser = User::factory()->create([
            'username' => 'johndoe',
            'email' => 'johndoe-private@example.com',
        ]);

        $viewerUser = User::factory()->create();

        $response = $this->actingAs($viewerUser)->getJson(
            route('api.users.show', ['username' => 'johndoe'])
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'username',
            ],
        ]);

        $response->assertJsonMissingPath('data.email');
    }

    /**
     * Ensures the stats returned contain correct post and friend counts.
     */
    #[Test]
    public function it_returns_correct_stats_counts(): void
    {
        $user = User::factory()
            ->has(Post::factory()->count(3), 'posts')
            ->create();

        $friend1 = User::factory()->create();
        $friend2 = User::factory()->create();

        $user->friendsOfMine()->attach($friend1->id, ['status' => 'accepted']);
        $user->friendsOfMine()->attach($friend2->id, ['status' => 'accepted']);

        $response = $this->getJson(
            route('api.users.show', ['username' => $user->username])
        );

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'stats' => [
                    'posts_count' => 3,
                    'friends_count' => 2,
                ],
            ],
        ]);
    }

    /**
     * Handles nullable fields such as bio properly.
     */
    #[Test]
    public function it_handles_nullable_fields_correctly(): void
    {
        $user = User::factory()->create([
            'username' => 'simpleuser',
            'bio' => null,
        ]);

        $response = $this->getJson(
            route('api.users.show', ['username' => 'simpleuser'])
        );

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'username' => 'simpleuser',
                'bio' => null,
            ],
        ]);
    }

    /**
     * Finds user regardless of username capitalization.
     */
    #[Test]
    public function it_finds_user_with_case_insensitive_username(): void
    {
        $user = User::factory()->create([
            'username' => 'johndoe',
        ]);

        $response = $this->getJson(
            route('api.users.show', ['username' => 'JohnDoe'])
        );

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'username' => 'johndoe',
            ],
        ]);
    }

    /**
     * Returns 404 error when user does not exist.
     */
    #[Test]
    public function test_it_returns_a_404_not_found_error_when_user_does_not_exist(): void
    {
        $response = $this->getJson(
            route('api.users.show', ['username' => 'nonexistentuser'])
        );

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'User not found',
        ]);
    }
}
