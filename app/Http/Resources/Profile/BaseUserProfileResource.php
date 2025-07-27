<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseUserProfileResource extends JsonResource
{
    protected function baseData(Request $request): array
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
