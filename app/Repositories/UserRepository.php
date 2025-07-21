<?php

namespace App\Repositories;

use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

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

    /**
     * Temukan user berdasarkan email dari akun Google, atau buat baru jika belum ada.
     * Jika user sudah ada, data nama dan google_id akan diperbarui.
     * Jika user belum ada, akun baru akan dibuat lengkap dengan username unik.
     *
     * @param SocialiteUser $googleUser Data user dari Google (via Socialite)
     * @return User User yang ditemukan atau baru dibuat
     */
    public function findOrCreateByGoogle(SocialiteUser $googleUser): User
    {
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $user->update([
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
            ]);
            return $user;
        }

        $username = str()->before($googleUser->getEmail(), '@') . '_' . str()->random(3);

        return User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'username' => $username,
            'google_id' => $googleUser->getId(),
            'password' => bcrypt(str()->random(16)),
        ]);
    }
}
