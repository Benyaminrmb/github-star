<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::get('github', [\App\Http\Controllers\Auth\GithubAuthController::class, 'redirect']);
    Route::get('github/callback', [\App\Http\Controllers\Auth\GithubAuthController::class, 'callback']);

});
