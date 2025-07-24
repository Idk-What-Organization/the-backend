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
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $startTime = microtime(true); // <-- Mulai timer
        Log::debug('AuthController: Register request received. Starting process...', [
            'email' => $request->input('email')
        ]);

        $result = $this->authService->register($request->validated(), $startTime);
        $totalTime = (microtime(true) - $startTime) * 1000;

        Log::info('AuthController: User successfully registered.', [
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
     * Authenticate a user and issue an access token.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            return response()->json([
                'message' => 'Login successful',
                'user' => $result['user'],
                'access_token' => $result['token'],
                'token_type' => 'Bearer',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid credentials',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Redirect the user to the Google authentication page.
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
