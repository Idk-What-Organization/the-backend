<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
            $validatedData = $request->validated();
            $rememberMe = $validatedData['remember_me'] ?? false;
            $result = $this->authService->login($validatedData, $startTime, $rememberMe);
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
        return Socialite::driver('google')->stateless()->redirect();
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

            return redirect('http://localhost:5173/login-success?token=' . $result['token']);

        } catch (Exception $e) {
            Log::error('AuthController: Google login failed.', [
                'error_message' => $e->getMessage(),
            ]);
            return redirect('http://localhost:5173/login-failed');
        }
    }

    /**
     * Log out the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }
}
