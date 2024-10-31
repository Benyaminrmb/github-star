<?php

use App\Http\Controllers\Auth\GithubAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::get('github', [GithubAuthController::class, 'redirect']);
    Route::get('github/callback', [GithubAuthController::class, 'callback']);
});
