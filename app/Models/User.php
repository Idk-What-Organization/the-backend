<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|User where(string $column, mixed $value)
 * @method static User create(array $attributes)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'google_id',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi model.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting atribut menjadi tipe data spesifik.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke semua post yang dibuat oleh user.
     *
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Relasi ke semua komentar yang dibuat oleh user.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relasi ke semua post yang disukai oleh user.
     *
     * @return BelongsToMany
     */
    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_likes');
    }

    /**
     * Relasi ke user yang ditambahkan sebagai teman oleh user ini.
     * (permintaan dikirim oleh saya dan sudah diterima)
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
     * Relasi ke user yang menambahkan saya sebagai teman.
     * (permintaan diterima oleh saya)
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
     * Accessor untuk mendapatkan semua teman (gabungan dari kedua arah).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFriendsAttribute()
    {
        if (!array_key_exists('friends', $this->relations)) {
            $friends = $this->friendsOfMine->merge($this->friendOf);
            $this->setRelation('friends', $friends);
        }
        return $this->getRelation('friends');
    }

    /**
     * Relasi untuk permintaan pertemanan yang dikirim oleh user ini.
     *
     * @return HasMany
     */
    public function sentFriendRequests(): HasMany
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    /**
     * Relasi untuk permintaan pertemanan yang diterima oleh user ini.
     *
     * @return HasMany
     */
    public function receivedFriendRequests(): HasMany
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }
}
