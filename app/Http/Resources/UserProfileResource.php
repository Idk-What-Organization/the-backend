<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * UserProfileResource
 *
 * Provides a representation of the user's profile data.
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string|null $bio
 * @property Carbon $created_at
 * @property string|null $photo_profile
 * @property string|null $photo_cover
 * @property int $posts_count
 * @property int $friends_of_mine_count
 * @property int $friend_of_count
 */
class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'bio' => $this->bio,
            'joined_at' => $this->created_at->toFormattedDateString(),
            'photos' => [
                'profile' => $this->photo_profile,
                'cover' => $this->photo_cover,
            ],
            'stats' => [
                'posts_count' => $this->posts_count,
                'friends_count' => $this->friends_of_mine_count + $this->friend_of_count,
            ],
        ];
    }
}
