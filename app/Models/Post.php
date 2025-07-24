<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'content',
    ];

    /**
     * Get the user who owns this post.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the images associated with this post.
     *
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class);
    }

    /**
     * Get the comments made on this post.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the hashtags attached to this post.
     *
     * @return BelongsToMany
     */
    public function hashtags(): BelongsToMany
    {
        return $this->belongsToMany(Hashtag::class, 'post_hashtag');
    }

    /**
     * Get the users who liked this post.
     *
     * @return BelongsToMany
     */
    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes');
    }
}
