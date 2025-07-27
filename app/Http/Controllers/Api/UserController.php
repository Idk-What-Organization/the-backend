<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Profile\PrivateUserProfileResource;
use App\Http\Resources\Profile\PublicUserProfileResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    /**
     * Display the profile of a user by username.
     *
     * Returns a private profile if the authenticated user is the same as the profile owner,
     * otherwise returns a public version of the profile.
     *
     * @param string $username The username of the user whose profile is to be displayed.
     * @return JsonResponse|PrivateUserProfileResource|PublicUserProfileResource
     */
    public function show(string $username): JsonResponse|PrivateUserProfileResource|PublicUserProfileResource
    {
        $user = $this->userService->getProfileByUsername($username);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return auth()->id() === $user->id
            ? new PrivateUserProfileResource($user)
            : new PublicUserProfileResource($user);
    }
}
