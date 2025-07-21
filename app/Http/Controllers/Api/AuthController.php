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

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Registrasi dan Autentikasi",
 *     description="Dokumentasi API untuk endpoint registrasi pengguna."
 * )
 */
class AuthController extends Controller
{
    protected AuthService $authService;

    /**
     * Inisialisasi AuthController dengan dependency AuthService.
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Registrasi user baru.
     *
     * @OA\Post(
     *     path="/api/register",
     *     summary="Registrasi user baru",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data untuk registrasi user baru",
     *         @OA\JsonContent(
     *             required={"name", "username", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Andi Budi"),
     *             @OA\Property(property="username", type="string", example="andibudi"),
     *             @OA\Property(property="email", type="string", format="email", example="andi.budi@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registrasi Berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User successfully registered"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="access_token", type="string", example="1|abcdef..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error Validasi",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $result['user'],
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Login dengan email/username dan password",
     *         @OA\JsonContent(
     *             required={"identity", "password"},
     *             @OA\Property(property="identity", type="string", format="email", example="andibudi", description="Bisa diisi dengan email atau username"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login Berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Kredensial tidak valid atau error validasi",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={
     *                     "identity": {"The identity field is required."},
     *                     "password": {"The password must be at least 8 characters."}
     *                 }
     *             )
     *         )
     *     )
     * )
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
     * Redirect pengguna ke halaman autentikasi Google.
     *
     * @return RedirectResponse
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback setelah autentikasi Google selesai.
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
