<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * Buat user baru di database.
     *
     * @param  array  $data  Data yang telah divalidasi untuk pembuatan user.
     * @return User  Instance user yang baru dibuat.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }
}
