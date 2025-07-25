<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProfileResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    /**
     * Display the user profile by username.
     *
     * @param string $username
     * @return UserProfileResource|JsonResponse
     */
    public function show(string $username): UserProfileResource|JsonResponse
    {
        $user = $this->userService->getProfileByUsername($username);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return new UserProfileResource($user);
    }
}
