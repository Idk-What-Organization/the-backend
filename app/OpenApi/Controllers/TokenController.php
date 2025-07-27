<?php

namespace App\OpenApi\Controllers;

/**
 * @OA\Tag(
 *     name="Token",
 *     description="Endpoints for token management"
 * )
 */
class TokenController
{
    /**
     * @OA\Post(
     *     path="/api/v1/auth/token/refresh",
     *     summary="Refresh an expired access token",
     *     tags={"Token"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL3YxL2F1dGgvbG9naW4iLCJpYXQiOjE2NzgyMzU2NzgsImV4cCI6MTY3ODIzOTI3OCwibmJmIjoxNjc4MjM1Njc4LCJqdGkiOiJkZDU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YiIsInN1YiI6IjEiLCJwcnYyM2JkNTYwZjQ3ZTRjNzViYzY1NDIwYzBhYmYzNTczZTI3YjkwNDg1In0.some_jwt_token_here"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600, description="Token expiration in seconds")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Could not refresh token, please log in again.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Could not refresh token, please log in again.")
     *         )
     *     )
     * )
     */
    public function refresh()
    {
    }
}