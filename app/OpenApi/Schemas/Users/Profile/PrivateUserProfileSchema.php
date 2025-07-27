<?php

namespace App\OpenApi\Schemas\Users\Profile;

/**
 * @OA\Schema(
 *     schema="PrivateUserProfile",
 *     type="object",
 *     title="Private User Profile",
 *     description="Full profile data visible only to the user themselves",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="username", type="string", example="john_doe"),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="phone", type="string", example="+628123456789"),
 *     @OA\Property(property="bio", type="string", example="Developer and coffee lover"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-01T00:00:00Z")
 * )
 */
class PrivateUserProfileSchema
{
}
