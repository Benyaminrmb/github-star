<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RepositoryResource;
use App\Repositories\Contracts\RepositoryInterface;
use App\Services\Github\GithubService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GithubController extends Controller
{
    private GithubService $githubService;
    private RepositoryInterface $repository;

    public function __construct(GithubService $githubService, RepositoryInterface $repository)
    {
        $this->githubService = $githubService;
        $this->repository = $repository;
    }

    public function syncStarred(Request $request): AnonymousResourceCollection
    {
        $request->validate(['username' => 'required|string']);

        $starredRepos = $this->githubService->getStarredRepositories($request->username);
        $this->repository->syncStarredRepositories($request->username, $starredRepos);

        return RepositoryResource::collection(
            $this->repository->findByUsername($request->username)
        );
    }

    public function getRateLimit(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->githubService->getRateLimit());
    }
}
