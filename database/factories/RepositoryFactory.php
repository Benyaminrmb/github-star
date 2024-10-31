<?php

namespace Database\Factories;

use App\Models\Repository;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepositoryFactory extends Factory
{
    protected $model = Repository::class;

    public function definition(): array
    {
        $username = $this->faker->userName();

        return [
            'github_id' => $this->faker->unique()->numberBetween(1000000, 9999999),
            'name' => $this->faker->slug(2),
            'description' => $this->faker->paragraph(),
            'url' => "https://github.com/{$username}/".$this->faker->slug(),
            'language' => $this->faker->randomElement(['PHP', 'JavaScript', 'Python', 'Java', 'Ruby']),
            'username' => $username,
        ];
    }
}
