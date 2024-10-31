<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        // Create unique programming-related tag names
        $tags = [
            'php', 'laravel', 'symfony', 'javascript', 'react', 'vue',
            'angular', 'python', 'django', 'flask', 'java', 'spring',
            'nodejs', 'express', 'typescript', 'golang', 'rust', 'c++',
            'docker', 'kubernetes', 'aws', 'devops', 'testing', 'api',
            'backend', 'frontend', 'fullstack', 'database', 'sql', 'nosql'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($tags),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
