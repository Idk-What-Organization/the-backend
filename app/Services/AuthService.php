<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Registered;

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
     * @return User Created user.
     */
    public function register(array $data, float $startTime): User
    {
        $timeToService = (microtime(true) - $startTime) * 1000;
        Log::debug('AuthService: Entered register method.', [
            'duration_to_service_ms' => round($timeToService),
        ]);

        $user = $this->userRepository->create($data, $startTime);

        // ======================= TAMBAHKAN BLOK INI =======================
        // Atur timestamp saat email verifikasi pertama akan dikirim
        $user->last_verification_email_sent_at = now();
        $user->save();
        // ==================================================================

        Log::debug('AuthService: User object created, firing Registered event.');
        event(new Registered($user));

        return $user;
    }

    /**
     * Handles the business logic for user login.
     *
     * @param array $credentials Validated login credentials.
     * @param float $startTime Time when the request handling started.
     * @return array{user: User, token: string} Logged-in user and authentication token.
     * @throws ValidationException When credentials are invalid.
     */
    public function login(array $credentials, float $startTime, bool $rememberMe = false): array
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

        if (!$user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Your email address is not verified. Please check your email for a verification link.'],
            ]);
        }

        // Set TTL for the access token based on rememberMe flag
        if ($rememberMe) {
            Auth::guard('api')->factory()->setTTL(10080); // 7 days
        } else {
            Auth::guard('api')->factory()->setTTL(60); // 1 hour
        }

        $token = Auth::guard('api')->login($user);

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
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = $this->userRepository->findOrCreateByGoogle($googleUser);
        $token = Auth::guard('api')->login($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Resend the email verification notification to the user.
     *
     * @param string $email
     * @return User
     * @throws ValidationException
     */
    public function resendVerificationEmail(string $email): User
    {
        $user = $this->userRepository->findByEmail($email);
        $user->last_verification_email_sent_at = now();
        $user->save();
        $user->sendEmailVerificationNotification();

        return $user;
    }
}
