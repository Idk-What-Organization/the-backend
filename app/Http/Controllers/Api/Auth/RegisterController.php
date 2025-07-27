<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
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

        $user = $this->authService->register($request->validated(), $startTime);
        $totalTime = (microtime(true) - $startTime) * 1000;

        Log::info('AuthController: User registered successfully. Verification email sent.', [
            'user_id' => $user->id,
            'total_duration_ms' => round($totalTime)
        ]);

        return response()->json([
            'message' => 'Registration successful. Please check your email to verify your account.',
        ], 201);
    }
}
