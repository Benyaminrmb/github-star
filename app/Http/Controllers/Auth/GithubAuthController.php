<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GithubAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GithubAuthController extends AuthController
{
    public function redirect()
    {
        return Socialite::driver('github')
            ->scopes(['read:user', 'user:email', 'repo'])
            ->redirect();
    }

    public function callback()
    {

            $githubUser = Socialite::driver('github')->stateless()->user();


            // Find or create user
            $user = User::updateOrCreate(
                ['email' => $githubUser->email],
                ['name' => $githubUser->name ?? $githubUser->nickname]
            );

            // Update or create GitHub account
            GithubAccount::updateOrCreate(
                ['github_id' => $githubUser->id],
                [
                    'user_id' => $user->id,
                    'github_username' => $githubUser->nickname,
                    'github_token' => $githubUser->token,
                    'github_refresh_token' => $githubUser->refreshToken,
                    'github_token_expires_at' => Carbon::now()->addSeconds($githubUser->expiresIn ?? 3600),
                ]
            );

            return $this->responseUser($user);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
