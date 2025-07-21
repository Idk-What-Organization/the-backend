<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthService
{
    protected UserRepository $userRepository;

    /**
     * Inisialisasi AuthService dengan UserRepository.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Proses logika bisnis untuk pendaftaran user baru.
     *
     * @param array $data Data registrasi yang telah divalidasi.
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

    /**
     * Proses logika bisnis untuk login user.
     *
     * @param array $credentials Data login yang telah divalidasi.
     * @return array{user: User, token: string}  User yang login dan token baru.
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $loginField = filter_var($credentials['identity'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!Auth::attempt([$loginField => $credentials['identity'], 'password' => $credentials['password']])) {
            throw ValidationException::withMessages([
                'identity' => ['Kredensial yang diberikan tidak cocok dengan data kami.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Handle the login process using Google OAuth.
     *
     * @return array{
     *     user: User,
     *     token: string
     * }
     */
    public function handleGoogleLogin(): array
    {
        $googleUser = Socialite::driver('google')->user();
        $user = $this->userRepository->findOrCreateByGoogle($googleUser);
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
