<?php

use App\Http\Controllers\Api\GithubController;
use App\Http\Controllers\Api\RepositoryController;
use App\Http\Controllers\Auth\GithubAuthController;
use App\Http\Middleware\EnsureGithubToken;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::get('github', [GithubAuthController::class, 'redirect']);
    Route::get('github/callback', [GithubAuthController::class, 'callback']);
    Route::post('logout', [GithubAuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Protected API routes
Route::middleware(['auth:sanctum', EnsureGithubToken::class])->group(function () {
    // GitHub endpoints
    Route::get('/sync-starred', [GithubController::class, 'syncStarred']);
    Route::get('/rate-limit', [GithubController::class, 'getRateLimit']);

    // Repository endpoints
    Route::get('/repositories', [RepositoryController::class, 'index']);
    Route::get('/repositories/search', [RepositoryController::class, 'search']);
    Route::post('/repositories/{repository}/tags', [RepositoryController::class, 'addTags']);
    Route::delete('/repositories/{repository}/tags', [RepositoryController::class, 'removeTags']);
});
