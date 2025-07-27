<?php

namespace App\OpenApi\Schemas\Users\Profile;

/**
 * @OA\Schema(
 *     schema="PublicUserProfile",
 *     type="object",
 *     title="Public User Profile",
 *     description="Profile data visible to other users",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="username", type="string", example="john_doe"),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="bio", type="string", example="Developer and coffee lover"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z")
 * )
 */
class PublicUserProfileSchema
{
}
