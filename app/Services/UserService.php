<?php

namespace App\Services;

use App\Models\User;
use App\Contracts\Repositories\UserRepository;
use App\Contracts\Services\UserService as UService;

use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class UserService extends Service implements UService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = app(UserRepository::class);
    }

    public function create(string $name, string $email, ?string $password, ?array $social): ?User
    {
        $fields = [
            'name' => $name,
            'email' => $email,
        ];

        if ($password) {
            $fields['password'] = Hash::make($password);
        } elseif ($social) {
            $fields['social'] = $social;
        } else {
            abort(422, 'No password or socialite data provided');
        }

        return $this->userRepository->create($fields);
    }

    public function getById(string $id): ?User
    {
        return $this->userRepository->getById($id);
    }

    public function getBySocialite(string $provider, SocialiteUser $user): ?User
    {
        return $this->userRepository->getBySocialite($provider, $user);
    }

    public function refreshSocialToken(User $user, array $social): bool
    {
        return $this->userRepository->refreshSocialToken($user, $social);
    }

    public function updateUserProfile(User $user, string $name, string $email, bool $deVerify = false): bool
    {
        return $this->userRepository->updateUserProfile($user, $name, $email, $deVerify);
    }

    public function updateUserPassword(User $user, string $newPassword): bool
    {
        return $this->userRepository->updateUserPassword($user, $newPassword);
    }
}
