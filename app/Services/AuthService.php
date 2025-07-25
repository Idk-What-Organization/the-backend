<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

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
     * @param float $startTime Time when the request handling started.
     * @return array{user: User, token: string} Created user and authentication token.
     */
    public function register(array $data, float $startTime): array
    {
        $timeToService = (microtime(true) - $startTime) * 1000;
        Log::debug('AuthService: Entered register method.', [
            'duration_to_service_ms' => round($timeToService),
        ]);

        $user = $this->userRepository->create($data, $startTime);
        Log::debug('AuthService: User object created, generating auth token.');
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
     * @param float $startTime Time when the request handling started.
     * @return array{user: User, token: string} Logged-in user and authentication token.
     * @throws ValidationException When credentials are invalid.
     */
    public function login(array $credentials, float $startTime): array
    {
        $timeToService = (microtime(true) - $startTime) * 1000;
        Log::debug('AuthService: Entered login method.', [
            'duration_to_service_ms' => round($timeToService),
        ]);

        $loginField = filter_var($credentials['identity'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        Log::debug('AuthService: Identity detected as ' . $loginField . '.');
        $authStartTime = microtime(true);
        if (!Auth::attempt([$loginField => $credentials['identity'], 'password' => $credentials['password']])) {
            $authDuration = (microtime(true) - $authStartTime) * 1000;
            Log::debug('AuthService: Auth::attempt failed.', [
                'duration_ms' => round($authDuration),
            ]);

            throw ValidationException::withMessages([
                'identity' => ['The provided credentials do not match our records.'],
            ]);
        }

        $authDuration = (microtime(true) - $authStartTime) * 1000;
        Log::debug('AuthService: Auth::attempt successful.', [
            'duration_ms' => round($authDuration),
        ]);

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
