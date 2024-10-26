<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RepositoryResource;
use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Http\Request;

class RepositoryController extends Controller
{
    private RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {

        $request->validate(['username' => 'required|string']);

        return RepositoryResource::collection(
            $this->repository->findByUsername($request->username)
        );
    }

    public function search(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'tag' => 'required|string'
        ]);

        return RepositoryResource::collection(
            $this->repository->findByTag($request->username, $request->tag)
        );
    }

    public function addTags(Request $request, $repositoryId)
    {
        $request->validate(['tags' => 'required|array']);

        $this->repository->addTags($repositoryId, $request->tags);

        return response()->json(['message' => 'Tags added successfully']);
    }

    public function removeTags(Request $request, $repositoryId)
    {
        $request->validate(['tags' => 'required|array']);

        $this->repository->removeTags($repositoryId, $request->tags);

        return response()->json(['message' => 'Tags removed successfully']);
    }
}

