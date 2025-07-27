<?php

namespace App\OpenApi\Controllers;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Endpoints for user authentication, registration, login, and logout"
 * )
 */
class RegisterController
{
    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
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
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL3YxL2F1dGgvbG9naW4iLCJpYXQiOjE2NzgyMzU2NzgsImV4cCI6MTY3ODIzOTI3OCwibmJmIjoxNjc4MjM1Njc4LCJqdGkiOiJkZDU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YiIsInN1YiI6IjEiLCJwcnYyM2JkNTYwZjQ3ZTRjNzViYzY1NDIwYzBhYmYzNTczZTI3YjkwNDg1In0.some_jwt_token_here"),
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
}