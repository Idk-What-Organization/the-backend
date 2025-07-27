<?php

namespace App\OpenApi\Controllers;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Endpoints for user authentication, login, and logout"
 * )
 */
class AuthController
{
    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     summary="Log in a user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Login using email or username and password",
     *         @OA\JsonContent(
     *             required={"identity", "password"},
     *             @OA\Property(property="identity", type="string", example="andibudi", description="Can be an email or a username"),
     *             @OA\Property(property="password", type="string", format="password", example="P@ssw0rd123!", description="User's password"),
     *             @OA\Property(property="remember_me", type="boolean", example=true, description="Set to true to keep the user logged in for a longer period (e.g., 7 days).")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="user", ref="#/components/schemas/UserAuth"),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL3YxL2F1dGgvbG9naW4iLCJpYXQiOjE2NzgyMzU2NzgsImV4cCI6MTY3ODIzOTI3OCwibmJmIjoxNjc4MjM1Njc4LCJqdGkiOiJkZDU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YiIsInN1YiI6IjEiLCJwcnYyM2JkNTYwZjQ3ZTRjNzViYzY1NDIwYzBhYmYzNTczZTI3YjkwNDg1In0.some_jwt_token_here"),
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
     *     path="/api/auth/google/redirect",
     *     summary="Redirect user to Google login page",
     *     tags={"Auth"},
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
     *     path="/api/v1/auth/logout",
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

    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     summary="Get the authenticated user's details",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserAuth")
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
    public function me()
    {
    }
}
