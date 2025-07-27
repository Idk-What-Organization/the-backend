<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;

class PublicUserProfileResource extends BaseUserProfileResource
{
    public function toArray(Request $request): array
    {
        return $this->baseData($request);
    }
}

