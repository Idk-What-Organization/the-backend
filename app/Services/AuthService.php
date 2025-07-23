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
     * Initialize AuthService with UserRepository.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handles the business logic for user registration.
     *
     * @param array $data Validated registration data.
     * @return array{user: User, token: string}  Created user and authentication token.
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
     * Handles the business logic for user login.
     *
     * @param array $credentials Validated login credentials.
     * @return array{user: User, token: string}  Logged-in user and new token.
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $loginField = filter_var($credentials['identity'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!Auth::attempt([$loginField => $credentials['identity'], 'password' => $credentials['password']])) {
            throw ValidationException::withMessages([
                'identity' => ['The provided credentials do not match our records.'],
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
