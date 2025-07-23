<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="Friendship",
 *     title="Friendship Model",
 *     description="Pivot model yang merepresentasikan relasi pertemanan antar user.",
 *     required={"user_id", "friend_id", "status"},
 *
 *     @OA\Property(property="user_id", type="integer", description="ID dari user yang mengirim permintaan pertemanan", example=1),
 *     @OA\Property(property="friend_id", type="integer", description="ID dari user yang menerima permintaan pertemanan", example=2),
 *     @OA\Property(property="status", type="string", description="Status pertemanan (e.g., pending, accepted, rejected)", example="accepted"),
 *
 *     @OA\Property(
 *         property="user",
 *         description="Data user pengirim permintaan pertemanan (jika relasi di-load)",
 *         ref="#/components/schemas/User"
 *     ),
 *
 *     @OA\Property(
 *         property="friend",
 *         description="Data user penerima permintaan pertemanan (jika relasi di-load)",
 *         ref="#/components/schemas/User"
 *     )
 * )
 */
class FriendshipSchema
{
}
