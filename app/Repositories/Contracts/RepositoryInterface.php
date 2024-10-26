<?php

namespace App\Repositories\Contracts;

interface RepositoryInterface
{
    public function syncStarredRepositories(string $username, array $repositories): void;

    public function findByUsername(string $username);

    public function findByTag(string $username, string $tag);

    public function addTags(int $repositoryId, array $tags);

    public function removeTags(int $repositoryId, array $tags);
}
