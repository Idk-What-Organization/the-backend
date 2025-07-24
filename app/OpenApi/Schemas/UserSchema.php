<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User Model",
 *     description="Model yang merepresentasikan data user dalam aplikasi.",
 *     required={"id", "name", "username", "email"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="name", type="string", description="Nama lengkap user", example="Andi Budi"),
 *     @OA\Property(property="username", type="string", description="Username unik user", example="andibudi"),
 *     @OA\Property(property="email", type="string", format="email", description="Alamat email unik user", example="andi.budi@example.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", readOnly=true, nullable=true, description="Waktu verifikasi email", example="2025-07-22T10:00:00.000000Z"),
 *     @OA\Property(property="google_id", type="string", nullable=true, description="ID unik dari Google untuk login sosial", example="109876543210987654321"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true, description="Waktu pembuatan record"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true, description="Waktu pembaruan record terakhir"),
 *     @OA\Property(
 *         property="posts",
 *         type="array",
 *         description="Daftar post yang dibuat oleh user",
 *         @OA\Items(ref="#/components/schemas/Post")
 *     ),
 *     @OA\Property(
 *         property="friends",
 *         type="array",
 *         description="Daftar teman dari user",
 *         @OA\Items(ref="#/components/schemas/User")
 *     )
 * )
 */
class UserSchema
{
}
