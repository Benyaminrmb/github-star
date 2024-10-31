<?php

namespace Tests\Feature\Api;

use App\Exceptions\GithubApiException;
use App\Models\GithubAccount;
use App\Models\User;
use App\Services\Github\GithubService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class GithubControllerTest extends TestCase
{
    use RefreshDatabase;

    protected GithubService $githubService;

    protected \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model $user;

    protected \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model $githubAccount;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock GitHub service
        $this->githubService = Mockery::mock(GithubService::class);
        $this->app->instance(GithubService::class, $this->githubService);

        // Create test user with GitHub account
        $this->user = User::factory()
            ->has(
                factory: GithubAccount::factory()->state([
                    'github_username' => 'testuser',
                    'github_token' => 'github_token_123',
                ])
            )->create();
        $this->githubAccount = $this->user->githubAccount;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_sync_starred_repositories_success(): void
    {
        // Mock starred repositories response
        $mockStarredRepos = [
            [
                'id' => 123456,
                'name' => 'test-repo',
                'full_name' => 'testuser/test-repo',
                'description' => 'Test repository',
                'html_url' => 'https://github.com/testuser/test-repo',
                'stargazers_count' => 100,
                'language' => 'PHP',
                'owner' => [
                    'login' => 'testuser',
                ],
            ],
            [
                'id' => 789012,
                'name' => 'another-repo',
                'full_name' => 'testuser/another-repo',
                'description' => 'Another test repository',
                'html_url' => 'https://github.com/testuser/another-repo',
                'stargazers_count' => 200,
                'language' => 'JavaScript',
                'owner' => [
                    'login' => 'testuser',
                ],
            ],
        ];

        // Set up mock expectations
        $this->githubService
            ->shouldReceive('getStarredRepositories')
            ->once()
            ->with($this->githubAccount->github_token)
            ->andReturn($mockStarredRepos);

        // Make request with authenticated user
        $response = $this->actingAs($this->user)
            ->getJson('/api/sync-starred');

        // Assert response is successful
        $response->assertStatus(200);

        // Assert repositories were stored in database
        $this->assertDatabaseHas('repositories', [
            'github_id' => 123456,
            'name' => 'test-repo',
            'description' => 'Test repository',
            'url' => 'https://github.com/testuser/test-repo',
            'language' => 'PHP',
        ]);

        $this->assertDatabaseHas('repositories', [
            'github_id' => 789012,
            'name' => 'another-repo',
            'description' => 'Another test repository',
            'url' => 'https://github.com/testuser/another-repo',
            'language' => 'JavaScript',
        ]);

        // Assert response structure
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'language',
                ],
            ],
        ]);
    }

    public function test_sync_starred_repositories_requires_authentication(): void
    {
        $response = $this->getJson('/api/sync-starred');
        $response->assertStatus(401);
    }

    public function test_sync_starred_repositories_handles_github_api_exception(): void
    {
        $this->githubService
            ->shouldReceive('getStarredRepositories')
            ->once()
            ->with($this->githubAccount->github_token)
            ->andThrow(new GithubApiException('API rate limit exceeded'));

        $response = $this->actingAs($this->user)
            ->getJson('/api/sync-starred');

        $response->assertStatus(500);
        $response->assertJson([
            'message' => 'API rate limit exceeded',
        ]);
    }
}
