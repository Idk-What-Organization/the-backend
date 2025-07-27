<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\Profile\PrivateUserProfileResource;
use App\Services\ProfileService;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService)
    {
    }

    /**
     * Update profil pengguna yang sedang terautentikasi.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $validatedData = $request->validated();

        $updatedUser = $this->profileService->updateProfile($user, $validatedData);

        return new PrivateUserProfileResource($updatedUser);
    }
}
