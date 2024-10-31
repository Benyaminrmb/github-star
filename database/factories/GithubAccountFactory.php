<?php

namespace Database\Factories;

use App\Models\GithubAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GithubAccountFactory extends Factory
{
    protected $model = GithubAccount::class;

    public function definition(): array
    {
        return [
            'github_id' => $this->faker->numberBetween(111111,999999),
            'github_username' => $this->faker->userName(),
            'github_token' => Str::random(10),
            'github_refresh_token' => Str::random(10),
            'github_token_expires_at' => Carbon::now()->addYear(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
