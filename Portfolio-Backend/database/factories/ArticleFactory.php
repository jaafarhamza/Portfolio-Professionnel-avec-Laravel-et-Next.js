<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Support\Str;
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
        $title = $this->faker->sentence();
        $content = $this->faker->paragraphs(10, true);
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->paragraph(),
            'content' => $content,
            'featured_image' => 'https://picsum.photos/1200/800?random=' . rand(1, 1000),
            'category_id' => Category::inRandomOrder()->first()->id ?? null,
            'reading_time' => ceil(str_word_count($content) / 200),
            'published' => true,
            'published_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}