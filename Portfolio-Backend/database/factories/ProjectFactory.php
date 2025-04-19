<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        $slug = Str::slug($title);

        return [
            'title' => $title,
            'slug' => $slug,
            'description' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(5, true),
            'thumbnail' => 'https://picsum.photos/640/480?random=' . rand(1, 1000),
            'images' => json_encode([
                'https://picsum.photos/800/600?random=' . rand(1, 1000),
                'https://picsum.photos/800/600?random=' . rand(1, 1000),
            ]),
            'url' => $this->faker->url(),
            'github_url' => 'https://github.com/username/' . $slug,
            'category' => $this->faker->randomElement(['Web Development', 'Mobile App', 'UI/UX Design']),
            'tags' => json_encode($this->faker->words(3)),
            'featured' => $this->faker->boolean(20),
            'order' => $this->faker->numberBetween(0, 10),
            'completed_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'published' => true,
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
