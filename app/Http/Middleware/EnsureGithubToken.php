<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGithubToken
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->githubAccount) {
            return response()->json(['error' => 'GitHub account not connected'], 401);
        }

        $githubAccount = $user->githubAccount;

        if (!$githubAccount->github_token) {
            return response()->json(['error' => 'GitHub token not found'], 401);
        }

        if ($githubAccount->github_token_expires_at && $githubAccount->github_token_expires_at->isPast()) {
            return response()->json(['error' => 'GitHub token expired'], 401);
        }

        return $next($request);
    }
}
