<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\VerifyEmailWithResend;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @method static Builder|User where(string $column, mixed $value)
 * @method static User create(array $attributes)
 */
class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use HasFactory, Notifiable;

    public mixed $id;

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailWithResend());
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'google_id',
        'bio',
        'photo_profile',
        'photo_cover',
        'background',
        'photo_parallax',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to specific types.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_verification_email_sent_at' => 'datetime',
        ];
    }

    /**
     * Get all posts created by the user.
     *
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get all comments made by the user.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get all posts liked by the user.
     *
     * @return BelongsToMany
     */
    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_likes');
    }

    /**
     * Get all users this user has added as friends (sent request and accepted).
     *
     * @return BelongsToMany
     */
    public function friendsOfMine(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->wherePivot('status', 'accepted')
            ->withTimestamps()
            ->using(Friendship::class);
    }

    /**
     * Get all users who added this user as a friend (request accepted by this user).
     *
     * @return BelongsToMany
     */
    public function friendOf(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
            ->wherePivot('status', 'accepted')
            ->withTimestamps()
            ->using(Friendship::class);
    }

    /**
     * Accessor to get all friends of this user (both directions merged).
     *
     * @return Collection
     */
    public function getFriendsAttribute(): Collection
    {
        if (!array_key_exists('friends', $this->relations)) {
            $friends = $this->friendsOfMine->merge($this->friendOf);
            $this->setRelation('friends', $friends);
        }
        return $this->getRelation('friends');
    }

    /**
     * Get all friend requests sent by this user.
     *
     * @return HasMany
     */
    public function sentFriendRequests(): HasMany
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    /**
     * Get all friend requests received by this user.
     *
     * @return HasMany
     */
    public function receivedFriendRequests(): HasMany
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
