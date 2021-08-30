<?php

namespace App\Actions\Fortify;

use App\Models;
use App\Contracts\Services\UserService;

use Laravel\Jetstream\Jetstream;
use Laravel\Fortify\Contracts\CreatesNewUsers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param array $input
     * @return ?Models\User
     * @throws ValidationException
     */
    public function create(array $input): ?Models\User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,NULL,id,deleted_at,NULL'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        return tap(app(UserService::class)->create($input['name'], $input['email'], $input['password'], null),
            function ($user) {
                $this->createTeam($user);
            }
        );
    }

    /**
     * Create a personal team for the user.
     *
     * @param Models\User $user
     * @return void
     */
    protected function createTeam(Models\User $user)
    {
        $user->ownedTeams()->save(Models\Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}
