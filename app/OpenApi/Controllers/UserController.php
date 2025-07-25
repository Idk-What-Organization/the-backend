<?php

namespace App\OpenApi\Controllers;

/**
 * @OA\Tag(
 *     name="User",
 *     description="Endpoints for managing user data"
 * )
 */
class UserController
{
    /**
     * @OA\Get(
     *     path="/api/users/{username}",
     *     summary="Get a user's profile information",
     *     description="Fetch public profile data for a specific user by their username. This is a public endpoint and does not require authentication.",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="username",
     *         in="path",
     *         required=true,
     *         description="The username of the user",
     *         @OA\Schema(type="string"),
     *         example="andibudi"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Andi Budi"),
     *                 @OA\Property(property="username", type="string", example="andibudi"),
     *                 @OA\Property(property="bio", type="string", example="Hello, world! This is my bio."),
     *                 @OA\Property(property="joined_at", type="string", example="July 25, 2025"),
     *                 @OA\Property(
     *                     property="photos",
     *                     type="object",
     *                     @OA\Property(property="profile", type="string", example="url/to/profile.jpg"),
     *                     @OA\Property(property="cover", type="string", example="url/to/cover.jpg")
     *                 ),
     *                 @OA\Property(
     *                     property="stats",
     *                     type="object",
     *                     @OA\Property(property="posts_count", type="integer", example=15),
     *                     @OA\Property(property="friends_count", type="integer", example=42)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function show(string $username)
    {
    }
}
