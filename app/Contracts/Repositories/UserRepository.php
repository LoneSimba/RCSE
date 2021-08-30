<?php

namespace App\Contracts\Repositories;

use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

interface UserRepository
{
    public function create(array $fields): ?User;

    public function getById(string $id): ?User;

    public function getBySocialite(string $provider, SocialiteUser $user): ?User;

    public function refreshSocialToken(User $user, array $social): bool;

    public function updateUserProfile(User $user, string $name, string $email, bool $deVerify = false): bool;

    public function updateUserPassword(User $user, string $newPassword): bool;
}
