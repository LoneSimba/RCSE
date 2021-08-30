<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Team;
use App\Http\Controllers\Controller;
use App\Contracts\Services\UserService;
use App\Providers\RouteServiceProvider;

use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class OAuthController extends Controller
{
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Redirect to authentication page based on $provider.
     *
     * @param string $provider
     * @return mixed|void
     */
    public function redirectToProvider(string $provider)
    {
        try {
            $scopes = config("services.$provider.scopes") ?? [];

            if (empty($scopes)) {
                return Socialite::driver($provider)->redirect();
            } else {
                return Socialite::driver($provider)->scopes($scopes)->redirect();
            }
        } catch (\Exception $e) {
            abort(404);
        }
    }

    /**
     * Obtain the user information from $provider
     *
     * @param string $provider
     * @return RedirectResponse
     */
    public function handleProviderCallback(UserService $userService, string $provider): RedirectResponse
    {
        try {
            $data = Socialite::driver($provider)->user();

            return $this->handleSocialUser($userService, $provider, $data);
        } catch (\Exception $e) {
            return redirect('login')->withErrors(['authentication_deny' => 'Login with '.ucfirst($provider).' failed. Please try again.']);
        }
    }

    /**
     * Handles the user's information and creates/updates
     * the record accordingly.
     *
     * @param UserService $userService
     * @param string $provider
     * @param SocialiteUser $data
     * @return RedirectResponse
     */
    public function handleSocialUser(UserService $userService, string $provider, SocialiteUser $data): RedirectResponse
    {
        $user = $userService->getBySocialite($provider, $data);

        if (!$user) {
            return $this->createUserWithSocialData($userService, $provider, $data);
        }

        $success = $this->socialLogin($user);

        if (!$success) {
            abort(400);
        }

        return $this->socialLogin($user);
    }

    /**
     * Create user
     *
     * @param string $provider
     * @param SocialiteUser $data
     * @return RedirectResponse
     */
    public function createUserWithSocialData(
        UserService $userService,
        string $provider,
        SocialiteUser $data
    ): RedirectResponse {
        try {
            $user = $userService->create(
                $data->name,
                $data->email,
                null,
                [
                    $provider => [
                        'id' => $data->id,
                        'token' => $data->token,
                    ],
                ]
            );

            $team = Team::forceCreate([
                'user_id' => $user->id,
                'name' => $user->name."'s Team",
                'personal_team' => true,
            ]);
            $user->current_team_id = $team->id;
            $user->save();

            return $this->socialLogin($user);
        } catch (\Exception $e) {
            return redirect('login')->withErrors(['authentication_deny' => 'Login with '.ucfirst($provider).' failed. Please try again.']);
        }
    }

    /**
     * Log the user in
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function socialLogin(User $user): RedirectResponse
    {
        auth()->loginUsingId($user->id);

        return redirect($this->redirectTo);
    }
}
