<?php
// app/OpenApi/Schemas/PostImageSchema.php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="PostImage",
 *     title="Post Image Model",
 *     description="Model yang merepresentasikan sebuah gambar yang terikat pada post.",
 *     required={"id", "post_id", "image_path"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=51),
 *     @OA\Property(property="post_id", type="integer", description="ID dari post pemilik gambar", example=101),
 *     @OA\Property(property="image_path", type="string", format="uri", description="Path atau URL ke file gambar", example="images/posts/image123.jpg")
 * )
 */
class PostImageSchema
{
}
