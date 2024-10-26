<?php

use App\Http\Controllers\Api\GithubController;
use App\Http\Controllers\Api\RepositoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:api'])->group(function () {
    Route::get('/aa', function (){
        dd('awd');
    });

    // GitHub endpoints
    Route::get('/sync-starred', [GithubController::class, 'syncStarred']);
    Route::get('/rate-limit', [GithubController::class, 'getRateLimit']);

    // Repository endpoints
    Route::get('/repositories', [RepositoryController::class, 'index']);
    Route::get('/repositories/search', [RepositoryController::class, 'search']);
    Route::post('/repositories/{repository}/tags', [RepositoryController::class, 'addTags']);
    Route::delete('/repositories/{repository}/tags', [RepositoryController::class, 'removeTags']);
});
