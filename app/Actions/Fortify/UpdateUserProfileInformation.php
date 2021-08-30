<?php

namespace App\Actions\Fortify;

use App\Contracts\Services\UserService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,NULL,id,deleted_at,NULL'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        /** @var UserService $userService */
        $userService = app(UserService::class);

        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $userService->updateUserProfile($user, $input['name'], $input['email'], true);
            $user->sendEmailVerificationNotification();
        } else {
            $userService->updateUserProfile($user, $input['name'], $input['email']);
        }
    }
}
