<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GithubAccount extends Model
{
    protected $fillable = [
        'user_id',
        'github_id',
        'github_username',
        'github_token',
        'github_refresh_token',
        'github_token_expires_at'
    ];

    protected $hidden = [
        'github_token',
        'github_refresh_token'
    ];

    protected $casts = [
        'github_token_expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
