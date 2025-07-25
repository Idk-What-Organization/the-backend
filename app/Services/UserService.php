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
     * Retrieve profile data for a user by username.
     *
     * @param string $username
     * @return User|null
     */
    public function getProfileByUsername(string $username): ?User
    {
        return $this->userRepository->findByUsernameWithCounts($username);
    }
}
