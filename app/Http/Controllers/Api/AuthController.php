<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

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
     * @param  AuthService  $authService
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
     * @param  RegisterRequest  $request
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
}
