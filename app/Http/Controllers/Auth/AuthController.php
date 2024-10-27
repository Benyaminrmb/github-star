<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\NewAccessToken;

class AuthController extends Controller
{

    public function loginByEmail(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => [
                'email',
                'required'
            ]
        ]);

        try {

            $user = User::whereEmail($request->email)->firstOrFail();
        } catch (\Exception $exception) {
            //todo we can try add some global json builder instead of using `response()->json` !
            return response()->json([
                'status' => false,
                'message' => 'no user found with this email.'
            ]);
        }

        return $this->responseUser($user);
    }

    protected function responseUser(User $user): \Illuminate\Http\JsonResponse
    {
        $token = $this->getToken($user);
        return response()->json(UserResource::make($user)->additional(['token' => $token]));
    }

    protected function getToken(User $user): string
    {

        if (!Auth::check()) {
            $this->loginUser($user);
        }
        return $user->createToken('github-token')->plainTextToken;
    }

    protected function loginUser(User $user): void
    {
        Auth::login($user);
    }
}
