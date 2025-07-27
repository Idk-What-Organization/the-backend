<?php

namespace App\OpenApi\Controllers;

/**
 * @OA\Tag(
 *     name="Profile",
 *     description="Endpoints for user profile management"
 * )
 */
class ProfileController
{
    /**
     * @OA\Put(
     *     path="/api/v1/profile",
     *     summary="Update the authenticated user's profile",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data to update user profile",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Andi Budi Updated"),
     *             @OA\Property(property="username", type="string", example="andibudi_updated"),
     *             @OA\Property(property="bio", type="string", example="Updated bio for Andi Budi.", nullable=true),
     *             @OA\Property(property="website", type="string", format="url", example="https://updated.example.com", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="user", ref="#/components/schemas/UserAuth")
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
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={
     *                 "username": {"The username has already been taken."}
     *             })
     *         )
     *     )
     * )
     */
    public function update()
    {
    }
}