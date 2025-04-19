<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skill>
 */
class SkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skillCategories = ['Programming', 'Design', 'DevOps', 'Soft Skills', 'Tools'];
        $name = $this->faker->unique()->word();
        
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'icon' => 'icon-' . Str::slug($name),
            'category' => $this->faker->randomElement($skillCategories),
            'proficiency' => $this->faker->numberBetween(60, 100),
            'is_highlighted' => $this->faker->boolean(30),
            'order' => $this->faker->numberBetween(0, 20),
        ];
    }
}