<?php

namespace Tests\Feature\Api;

use App\Models\GithubAccount;
use App\Models\Repository;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepositoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $githubAccount;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user with GitHub account
        $this->user = User::factory()
            ->has(
                factory: GithubAccount::factory()->state([
                    'github_username' => 'testuser',
                    'github_token' => 'github_token_123',
                ])
            )->create();
        $this->githubAccount = $this->user->githubAccount;

        // Create test repository
        $this->repository = Repository::factory()->create([
            'github_id' => 123456,
            'name' => 'test-repo',
            'description' => 'Test repository',
            'url' => 'https://github.com/testuser/test-repo',
            'language' => 'PHP',
            'username' => 'testuser',
        ]);
    }

    public function test_index_returns_repositories_for_username(): void
    {
        Repository::factory()->count(3)->create([
            'username' => 'testuser',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/repositories?username=testuser');

        $response->assertStatus(200);
        $response->assertJsonCount(4, 'data'); // 3 new + 1 from setUp
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'url',
                    'language',
                    'username',
                    'tags',
                ],
            ],
        ]);
    }

    public function test_add_tags_to_repository(): void
    {
        $tags = ['php', 'laravel', 'api'];

        $response = $this->actingAs($this->user)
            ->postJson("/api/repositories/{$this->repository->id}/tags", [
                'tags' => $tags,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Tags added successfully']);

        // Verify tags were added to the repository
        foreach ($tags as $tagName) {
            $this->assertDatabaseHas('tags', ['name' => $tagName]);
            $this->assertDatabaseHas('repository_tag', [
                'repository_id' => $this->repository->id,
                'tag_id' => Tag::where('name', $tagName)->first()->id,
            ]);
        }
    }

    public function test_remove_tags_from_repository(): void
    {
        // First add some tags
        $tags = ['php', 'laravel', 'api'];
        foreach ($tags as $tagName) {
            $tag = Tag::factory()->create(['name' => $tagName]);
            $this->repository->tags()->attach($tag->id);
        }


        // Remove specific tags
        $tagsToRemove = ['php', 'laravel'];
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/repositories/{$this->repository->id}/tags", [
                'tags' => $tagsToRemove,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Tags removed successfully']);

        // Verify tags were removed
        foreach ($tagsToRemove as $tagName) {
            $this->assertDatabaseMissing('tags', [

                'name' => $tagName,
            ]);
        }

        // Verify 'api' tag still exists
        $this->assertDatabaseHas('repository_tag', [
            'repository_id' => $this->repository->id,
            'tag_id' => Tag::where('name', 'api')->first()->id,
        ]);
    }

    public function test_search_repositories_by_tag(): void
    {
        // Create additional repositories with different tags
        $repo1 = Repository::factory()->create(['username' => 'testuser']);
        $repo2 = Repository::factory()->create(['username' => 'testuser']);

        // Create and attach tags
        $phpTag = Tag::factory()->create(['name' => 'php']);
        $laravelTag = Tag::factory()->create(['name' => 'laravel']);
        $laragonTag = Tag::factory()->create(['name' => 'laragon']);

        $this->repository->tags()->attach($phpTag->id);
        $repo1->tags()->attach([$phpTag->id, $laravelTag->id]);
        $repo2->tags()->attach($laravelTag->id);
        $repo1->tags()->attach($laragonTag->id);
        $repo2->tags()->attach([$phpTag->id, $laragonTag->id]);

        // Search for repositories with 'php' tag
        $response = $this->actingAs($this->user)
            ->getJson('/api/repositories/search?username=testuser&tag=lara');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data'); // Should find 2 repositories with 'lara' tag

        // Verify response structure and content
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'url',
                    'language',
                    'username',
                    'tags' => [
                        '*' => [
                            'id',
                            'name',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_add_tags_validation(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/repositories/{$this->repository->id}/tags", [
                'tags' => 'not-an-array', // Should be array
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tags']);
    }

    public function test_search_repositories_validation(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/repositories/search');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['username', 'tag']);
    }

    public function test_index_requires_username(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/repositories');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['username']);
    }
}
