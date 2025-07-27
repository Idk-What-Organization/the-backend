<?php

namespace App\OpenApi\Controllers;

/**
 * @OA\Tag(
 *     name="Email Verification",
 *     description="Endpoints for email verification"
 * )
 */
class EmailVerificationController
{
    /**
     * @OA\Get(
     *     path="/api/v1/email/verify/{id}/{hash}",
     *     summary="Verify user email address",
     *     tags={"Email Verification"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="hash",
     *         in="path",
     *         required=true,
     *         description="Verification hash",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email verified successfully!"),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL3YxL2F1dGgvbG9naW4iLCJpYXQiOjE2NzgyMzU2NzgsImV4cCI6MTY3ODIzOTI3OCwibmJmIjoxNjc4MjM1Njc4LCJqdGkiOiJkZDU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YjU2YiIsInN1YiI6IjEiLCJwcnYyM2JkNTYwZjQ3ZTRjNzViYzY1NDIwYzBhYmYzNTczZTI3YjkwNDg1In0.some_jwt_token_here"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid verification link.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid verification link.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Email already verified.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email already verified.")
     *         )
     *     )
     * )
     */
    public function verify()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/email/resend/{user}",
     *     summary="Resend email verification link without authentication",
     *     tags={"Email Verification"},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A new verification link has been sent to your email.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="A new verification link has been sent to your email.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={
     *                 "email": {"The email field is required."}
     *             })
     *         )
     *     )
     * )
     */
    public function resendWithoutAuth()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/email/resend-verification",
     *     summary="Resend email verification link",
     *     tags={"Email Verification"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Email address to resend verification link to",
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verifikasi telah berhasil dikirim ulang.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email verifikasi telah berhasil dikirim ulang.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Gagal mengirim ulang email verifikasi."),
     *             @OA\Property(property="errors", type="object", example={
     *                 "email": {"The email field is required."}
     *             })
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Terjadi kesalahan server saat mencoba mengirim ulang email verifikasi.")
     *         )
     *     )
     * )
     */
    public function resendVerificationEmail()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/email/verification-notification",
     *     summary="Send email verification notification to authenticated user",
     *     tags={"Email Verification"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="A new verification link has been sent to your email address.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="A new verification link has been sent to your email address.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many requests",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Too many requests. Please try again in 60 seconds.")
     *         )
     *     )
     * )
     */
    public function sendVerificationNotification()
    {
    }
}