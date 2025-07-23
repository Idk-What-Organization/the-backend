<?php

namespace App\OpenApi\Controllers;

/**
 * @OA\Tag(
 * name="Auth",
 * description="Endpoint untuk autentikasi, registrasi, dan login user"
 * )
 */
class AuthController
{
    /**
     * @OA\Post(
     * path="/api/register",
     * summary="Registrasi user baru",
     * tags={"Auth"},
     * @OA\RequestBody(
     * required=true,
     * description="Data untuk registrasi user baru",
     * @OA\JsonContent(
     * required={"name", "username", "email", "password", "password_confirmation"},
     * @OA\Property(property="name", type="string", example="Andi Budi"),
     * @OA\Property(property="username", type="string", example="andibudi"),
     * @OA\Property(property="email", type="string", format="email", example="andi.budi@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Registrasi Berhasil",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="User successfully registered"),
     * @OA\Property(property="user", ref="#/components/schemas/User"),
     * @OA\Property(property="access_token", type="string", example="1|abcdef..."),
     * @OA\Property(property="token_type", type="string", example="Bearer")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Error Validasi",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The username has already been taken. (and 1 more error)"),
     * @OA\Property(
     * property="errors",
     * type="object",
     * example={
     * "username": {"The username has already been taken."},
     * "email": {"The email has already been taken."}
     * }
     * )
     * )
     * )
     * )
     */
    public function register() {}

    /**
     * @OA\Post(
     * path="/api/login",
     * summary="Login user",
     * tags={"Auth"},
     * @OA\RequestBody(
     * required=true,
     * description="Login dengan email/username dan password",
     * @OA\JsonContent(
     * required={"identity", "password"},
     * @OA\Property(property="identity", type="string", format="email", example="andibudi", description="Bisa diisi dengan email atau username"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Login Berhasil",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Login successful"),
     * @OA\Property(property="user", ref="#/components/schemas/User"),
     * @OA\Property(property="access_token", type="string"),
     * @OA\Property(property="token_type", type="string", example="Bearer")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Kredensial tidak valid atau error validasi",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Invalid credentials"),
     * @OA\Property(
     * property="errors",
     * type="object",
     * example={
     * "identity": {"The identity field is required."},
     * "password": {"The password must be at least 8 characters."}
     * }
     * )
     * )
     * )
     * )
     */
    public function login() {}
}
