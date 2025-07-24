<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Http\Requests\Auth\LoginRequest;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected AuthService $authService;

    /**
     * Initialize AuthController with AuthService dependency.
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle user registration request.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $startTime = microtime(true);

        Log::debug('AuthController: Register request received.', [
            'email' => $request->input('email')
        ]);

        $result = $this->authService->register($request->validated(), $startTime);
        $totalTime = (microtime(true) - $startTime) * 1000;

        Log::info('AuthController: User registered successfully.', [
            'user_id' => $result['user']->id,
            'total_duration_ms' => round($totalTime)
        ]);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $result['user'],
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Handle user login request and issue access token.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $startTime = microtime(true);

        Log::debug('AuthController: Login request received.', [
            'identity' => $request->input('identity'),
            'ip_address' => $request->ip()
        ]);

        try {
            $result = $this->authService->login($request->validated(), $startTime);
            $totalTime = (microtime(true) - $startTime) * 1000;

            Log::info('AuthController: Login successful.', [
                'user_id' => $result['user']->id,
                'ip_address' => $request->ip(),
                'duration_ms' => round($totalTime),
            ]);

            return response()->json([
                'message' => 'Login successful',
                'user' => $result['user'],
                'access_token' => $result['token'],
                'token_type' => 'Bearer',
            ]);

        } catch (ValidationException $e) {
            $durationToFailure = (microtime(true) - $startTime) * 1000;

            Log::warning('AuthController: Login failed - invalid credentials.', [
                'identity' => $request->input('identity'),
                'ip_address' => $request->ip(),
                'duration_ms' => round($durationToFailure),
                'errors' => $e->errors(),
            ]);

            return response()->json([
                'message' => 'Invalid credentials',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Redirect user to Google OAuth page.
     *
     * @return RedirectResponse
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback after Google authentication.
     *
     * @return RedirectResponse
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $result = $this->authService->handleGoogleLogin();

            return redirect('http://localhost:3000/login-success?token=' . $result['token']);

        } catch (Exception $e) {
            return redirect('http://localhost:3000/login-failed?error=' . $e->getMessage());
        }
    }
}
