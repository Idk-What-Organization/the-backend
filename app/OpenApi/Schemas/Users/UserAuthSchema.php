<?php

namespace App\OpenApi\Schemas\Users;

/**
 * @OA\Schema(
 *     schema="UserAuth",
 *     title="User Auth Schema",
 *     description="Schema untuk response data user saat login atau registrasi.",
 *     required={"id", "name", "username", "email"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID unik user",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nama lengkap user",
 *         example="Andi Budi"
 *     ),
 *     @OA\Property(
 *         property="username",
 *         type="string",
 *         description="Username unik user",
 *         example="andibudi"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Alamat email user",
 *         example="andi.budi@example.com"
 *     )
 * )
 */
class UserAuthSchema
{
}
