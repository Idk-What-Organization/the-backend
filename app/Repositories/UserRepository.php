<?php

namespace App\Repositories;

use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class UserRepository
{
    /**
     * Create a new user in the database.
     *
     * @param  array  $data  Validated data for creating a user.
     * @return User  The newly created user instance.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Find a user by their Google account email, or create a new one if not found.
     * If the user exists, their name and google_id will be updated.
     * If not, a new user will be created with a unique username.
     *
     * @param SocialiteUser $googleUser User data from Google (via Socialite).
     * @return User The found or newly created user.
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
