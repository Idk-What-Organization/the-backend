<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * Retrieve the public profile data of a user by their username.
     *
     * This includes user info and counts of related data like posts and friends.
     *
     * @param string $username The username of the user to retrieve.
     * @return User|null The user model with profile data, or null if not found.
     */
    public function getProfileByUsername(string $username): ?User
    {
        return $this->userRepository->findByUsernameWithCounts($username);
    }
}
