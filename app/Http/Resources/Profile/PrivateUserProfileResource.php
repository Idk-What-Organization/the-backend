<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;

class PrivateUserProfileResource extends BaseUserProfileResource
{
    public function toArray(Request $request): array
    {
        return array_merge(
            $this->baseData($request),
            [
                'email' => $this->email,
            ]
        );
    }
}

