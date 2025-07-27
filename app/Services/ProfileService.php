<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class ProfileService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * Memperbarui data profil pengguna.
     */
    public function updateProfile(User $user, array $data): User
    {
        $this->userRepository->update($user->id, $data);

        // Mengembalikan model user yang sudah diperbarui
        return $user->fresh();
    }
}
