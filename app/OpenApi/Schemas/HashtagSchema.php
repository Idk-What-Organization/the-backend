<?php
// app/OpenApi/Schemas/HashtagSchema.php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="Hashtag",
 *     title="Hashtag Model",
 *     description="Model yang merepresentasikan sebuah hashtag.",
 *     required={"id", "tag"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=34),
 *     @OA\Property(property="tag", type="string", description="Nama tag unik tanpa karakter '#'", example="blessed")
 * )
 */
class HashtagSchema
{
}
