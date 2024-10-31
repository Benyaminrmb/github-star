<?php
namespace App\Http\Controllers\Api;

use App\Exceptions\GithubApiException;
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

    /**
     * @throws GithubApiException
     */
    public function syncStarred(Request $request): AnonymousResourceCollection
    {
        $githubAccount = $request->user()->githubAccount;

        $starredRepos = $this->githubService->getStarredRepositories($githubAccount->github_token);

        $this->repository->syncStarredRepositories($githubAccount->github_username, $starredRepos);

        return RepositoryResource::collection(
            $this->repository->findByUsername($githubAccount->github_username)
        );
    }

    public function getRateLimit(Request $request)
    {
        return response()->json(
            $this->githubService->getRateLimit($request->user()->githubAccount->github_token)
        );
    }
}
