<?php

namespace App\Repositories;

use App\Models\User;
use App\Contracts\Repositories\UserRepository as URepository;

use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class UserRepository extends Repository implements URepository
{
    public function create(array $fields): ?User
    {
        return $this->transaction('User', function () use ($fields) {
            return $this->getModel()->create($fields);
        });
    }

    public function getById(string $id): ?User
    {
        return $this->getModel()->find($id);
    }

    public function getBySocialite(string $provider, SocialiteUser $user): ?User
    {
        return $this->getModel()
            ->where("social->{$provider}->id", $user->id)
            ->orWhere('email', $user->email)
            ->first();
    }

    public function refreshSocialToken(User $user, array $social): bool
    {
        return $user->update($social);
    }

    public function updateUserProfile(User $user, string $name, string $email, bool $deVerify = false): bool
    {
        $fields = [
            'name' => $name,
            'email' => $email
        ];

        if ($deVerify) {
            $fields['email_verified_at'] = null;
        }

        return $user->update($fields);
    }

    public function updateUserPassword(User $user, string $newPassword): bool
    {
        return $user->update(['password' => Hash::make($newPassword)]);
    }
}
