<?php

namespace App\OpenApi\Controllers;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Endpoints for user authentication, registration, login, and logout"
 * )
 */
class AuthController
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data required to register a new user",
     *         @OA\JsonContent(
     *             required={"name", "username", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Andi Budi"),
     *             @OA\Property(property="username", type="string", example="andibudi"),
     *             @OA\Property(property="email", type="string", format="email", example="andi.budi@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="P@ssw0rd123!", description="Must be at least 8 characters and include uppercase, lowercase, number, and symbol."),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="P@ssw0rd123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User successfully registered"),
     *             @OA\Property(property="user", ref="#/components/schemas/UserAuth"),
     *             @OA\Property(property="access_token", type="string", example="1|abcdef..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation Failed! Please check the data you submitted."),
     *             @OA\Property(property="errors", type="object", example={
     *                 "username": {"The username has already been taken."},
     *                 "email": {"The email has already been taken."}
     *             })
     *         )
     *     )
     * )
     */
    public function register()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Log in a user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Login using email or username and password",
     *         @OA\JsonContent(
     *             required={"identity", "password"},
     *             @OA\Property(property="identity", type="string", example="andibudi", description="Can be an email or a username"),
     *             @OA\Property(property="password", type="string", format="password", example="P@ssw0rd123!", description="User's password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="user", ref="#/components/schemas/UserAuth"),
     *             @OA\Property(property="access_token", type="string", example="1|abcdef..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid credentials or validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed! Please check the data you submitted."),
     *             @OA\Property(property="errors", type="object", example={
     *                 "identity": {"The provided credentials do not match our records."}
     *             })
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many login attempts",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Too many requests. Please try again in 60 seconds."),
     *             @OA\Property(property="retry_after_seconds", type="string", example="60")
     *         )
     *     )
     * )
     */
    public function login()
    {
    }

    /**
     * @OA\Get(
     *     path="/auth/google/redirect",
     *     summary="Redirect user to Google login page",
     *     tags={"OAuth"},
     *     description="This endpoint redirects the user to the Google login page. It cannot be executed directly from Swagger UI.",
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to Google login"
     *     )
     * )
     */
    public function googleRedirect()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Log out the currently authenticated user",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function logout()
    {
    }
}
