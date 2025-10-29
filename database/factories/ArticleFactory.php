<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();
        
        return [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title) . '-' . fake()->unique()->randomNumber(5),
            'category_id' => \App\Models\Category::factory(),
            'tags' => fake()->randomElements(['php', 'laravel', 'programming', 'web', 'backend'], rand(1, 3)),
            'content' => fake()->paragraphs(3, true),
            'image' => 'articles/' . fake()->image('public/storage/articles', 640, 480, null, false),
        ];
    }
}
