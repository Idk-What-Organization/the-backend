<?php
// app/OpenApi/Schemas/CommentSchema.php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="Comment",
 *     title="Comment Model",
 *     description="Model yang merepresentasikan data komentar pada sebuah post.",
 *     required={"id", "user_id", "post_id", "body"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=201),
 *     @OA\Property(property="user_id", type="integer", description="ID dari user yang berkomentar", example=1),
 *     @OA\Property(property="post_id", type="integer", description="ID dari post yang dikomentari", example=101),
 *     @OA\Property(property="parent_id", type="integer", nullable=true, description="ID dari komentar induk (jika ini adalah balasan)", example=null),
 *     @OA\Property(property="body", type="string", description="Isi dari komentar", example="Post yang sangat menginspirasi!"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true),
 *     @OA\Property(
 *         property="user",
 *         description="Data user yang membuat komentar",
 *         ref="#/components/schemas/User"
 *     ),
 *     @OA\Property(
 *         property="replies",
 *         type="array",
 *         description="Daftar balasan untuk komentar ini",
 *         @OA\Items(ref="#/components/schemas/Comment")
 *     )
 * )
 */
class CommentSchema
{
}
