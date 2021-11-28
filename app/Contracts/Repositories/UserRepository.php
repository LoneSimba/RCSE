<?php

namespace App\Contracts\Repositories;

use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

interface UserRepository
{
    public function create(array $fields): ?User;

    public function getById(string $id): ?User;

    public function getBySocialite(string $provider, SocialiteUser $user): ?User;

    public function updateSocialToken(User $user, array $social): bool;

    public function updateProfile(User $user, string $name, string $email, bool $deVerify = false): bool;

    public function updatePassword(User $user, string $newPassword): bool;

    public function updateGroup(User $user, string $groupId): bool;
}
