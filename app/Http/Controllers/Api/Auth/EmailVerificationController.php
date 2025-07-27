<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Auth\ResendVerificationEmailRequest;
use Exception;

class EmailVerificationController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Mark the user's email address as verified.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        $user = User::findOrFail($request->route('id'));

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        $token = auth('api')->login($user);

        return response()->json([
            'message' => 'Email verified successfully!',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Resend the email verification notification without authentication.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function resendWithoutAuth(User $user): JsonResponse
    {
        try {
            $this->authService->resendVerificationEmail($user->email);

            return response()->json(['message' => 'A new verification link has been sent to your email.']);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Resend the email verification notification.
     *
     * @param ResendVerificationEmailRequest $request
     * @return JsonResponse
     */
    public function resendVerificationEmail(ResendVerificationEmailRequest $request): JsonResponse
    {
        try {
            $this->authService->resendVerificationEmail($request->email);

            return response()->json([
                'message' => 'Email verifikasi telah berhasil dikirim ulang.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Gagal mengirim ulang email verifikasi.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('EmailVerificationController: Failed to resend verification email.', [
                'error_message' => $e->getMessage(),
                'email' => $request->email,
            ]);
            return response()->json([
                'message' => 'Terjadi kesalahan server saat mencoba mengirim ulang email verifikasi.',
            ], 500);
        }
    }
}
