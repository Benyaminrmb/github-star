<?php

namespace App\Services\Github;

use App\Exceptions\GithubApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GithubService
{
    private string $baseUrl;

    private const CACHE_TTL = 3600; // 1 hour

    public function __construct()
    {
        $this->baseUrl = config('services.github.api_url', 'https://api.github.com');
    }

    /**
     * @throws GithubApiException
     */
    public function getStarredRepositories(?string $token = null): array
    {
        if (! $token) {
            throw new GithubApiException('GitHub token is required');
        }

        $cacheKey = 'github_stars_'.md5($token);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($token) {
            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/user/starred", [
                    'per_page' => 100,
                    'accept' => 'application/vnd.github.v3+json',
                ]);

            if (! $response->successful()) {
                throw new GithubApiException(
                    "Failed to fetch starred repositories: {$response->body()}",
                    $response->status()
                );
            }

            return $response->json();
        });
    }

    /**
     * @throws ConnectionException
     */
    public function getRateLimit(string $token): array
    {
        $response = Http::withToken($token)->get("{$this->baseUrl}/rate_limit");

        return $response->json()['resources']['core'] ?? [];
    }
}
