<?php

namespace App\OpenApi\Controllers;

/**
 * @OA\Tag(
 *     name="User",
 *     description="Endpoints for user information"
 * )
 */
class UserController
{
    /**
     * @OA\Get(
     *     path="/api/v1/users/{username}",
     *     summary="Display the profile of a user by username",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="username",
     *         in="path",
     *         required=true,
     *         description="The username of the user whose profile is to be displayed.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User profile retrieved successfully"),
     *             @OA\Property(property="user", ref="#/components/schemas/UserAuth")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function show()
    {
    }
}