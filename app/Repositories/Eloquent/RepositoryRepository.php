<?php

namespace App\Repositories\Eloquent;

use App\Models\Repository;
use App\Models\Tag;
use App\Repositories\Contracts\RepositoryInterface;

class RepositoryRepository implements RepositoryInterface
{
    public function syncStarredRepositories(string $username, array $repositories): void
    {
        foreach ($repositories as $repo) {
            Repository::updateOrCreate(
                ['github_id' => $repo['id']],
                [
                    'name' => $repo['name'],
                    'description' => $repo['description'],
                    'url' => $repo['html_url'],
                    'language' => $repo['language'],
                    'username' => $username
                ]
            );
        }
    }

    public function findByUsername(string $username)
    {
        return Repository::where('username', $username)
            ->with('tags')
            ->get();
    }

    public function findByTag(string $username, string $tag)
    {
        return Repository::where('username', $username)
            ->withTag($tag)
            ->with('tags')
            ->get();
    }

    public function addTags(int $repositoryId, array $tags): void
    {
        $repository = Repository::findOrFail($repositoryId);

        $tagIds = collect($tags)->map(function ($tagName) {
            return Tag::firstOrCreate(['name' => $tagName])->id;
        });

        $repository->tags()->syncWithoutDetaching($tagIds);
    }

    public function removeTags(int $repositoryId, array $tags): void
    {
        $repository = Repository::findOrFail($repositoryId);
        $tagIds = Tag::whereIn('name', $tags);
        $repository->tags()->detach($tagIds->pluck('id'));
        $tagIds->delete();
    }
}
