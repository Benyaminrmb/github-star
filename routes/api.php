<?php

use App\Http\Controllers\Api\GithubController;
use App\Http\Controllers\Api\RepositoryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GithubAuthController;
use App\Http\Middleware\EnsureGithubToken;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('/login-by-email', [AuthController::class, 'loginByEmail']);
});


// Protected API routes
Route::middleware(['auth:sanctum', EnsureGithubToken::class])->group(function () {
    // GitHub Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    // GitHub endpoints
    Route::get('/sync-starred', [GithubController::class, 'syncStarred']);
    Route::get('/rate-limit', [GithubController::class, 'getRateLimit']);

    // Repository endpoints
    Route::get('/repositories', [RepositoryController::class, 'index']);
    Route::get('/repositories/search', [RepositoryController::class, 'search']);
    Route::post('/repositories/{repository}/tags', [RepositoryController::class, 'addTags']);
    Route::delete('/repositories/{repository}/tags', [RepositoryController::class, 'removeTags']);
});
