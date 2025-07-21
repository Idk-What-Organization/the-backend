<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;

class AuthService
{
    protected UserRepository $userRepository;

    /**
     * Inisialisasi AuthService dengan UserRepository.
     *
     * @param  UserRepository  $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Proses logika bisnis untuk pendaftaran user baru.
     *
     * @param  array  $data  Data registrasi yang telah divalidasi.
     * @return array{user: User, token: string}  User yang dibuat dan token autentikasi.
     */
    public function register(array $data): array
    {
        $user = $this->userRepository->create($data);
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
