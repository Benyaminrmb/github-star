<?php

namespace App\Services\Github;

use App\Exceptions\GithubApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GithubService
{
    private string $baseUrl;
    private const CACHE_TTL = 3600; // 1 hour

    public function __construct()
    {
        $this->baseUrl = config('services.github.api_url', 'https://api.github.com');
    }

    public function getStarredRepositories(string $username): array
    {
        $cacheKey = "github_stars_{$username}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($username) {
            $response = Http::get("{$this->baseUrl}/users/{$username}/starred", [
                'per_page' => 100,
                'accept' => 'application/vnd.github.v3+json',
            ]);

            if (!$response->successful()) {
                throw new GithubApiException(
                    "Failed to fetch starred repositories: {$response->body()}",
                    $response->status()
                );
            }

            return $response->json();
        });
    }

    public function getRateLimit(): array
    {
        $response = Http::get("{$this->baseUrl}/rate_limit");
        return $response->json()['resources']['core'] ?? [];
    }
}
