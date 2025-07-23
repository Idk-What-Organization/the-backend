<?php
// app/OpenApi/Schemas/PostSchema.php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="Post",
 *     title="Post Model",
 *     description="Model yang merepresentasikan data post.",
 *     required={"id", "user_id", "content"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=101),
 *     @OA\Property(property="user_id", type="integer", description="ID dari user pemilik post", example=1),
 *     @OA\Property(property="content", type="string", description="Isi konten dari post", example="Lihat pemandangan indah hari ini! #blessed"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true),
 *     @OA\Property(
 *         property="user",
 *         description="Data user pemilik post (jika relasi di-load)",
 *         ref="#/components/schemas/User"
 *     ),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         description="Daftar gambar yang terasosiasi dengan post ini",
 *         @OA\Items(ref="#/components/schemas/PostImage")
 *     ),
 *     @OA\Property(
 *         property="comments",
 *         type="array",
 *         description="Daftar komentar pada post ini",
 *         @OA\Items(ref="#/components/schemas/Comment")
 *     ),
 *     @OA\Property(
 *         property="likers",
 *         type="array",
 *         description="Daftar user yang menyukai post ini",
 *         @OA\Items(ref="#/components/schemas/User")
 *     )
 * )
 */
class PostSchema
{
}
