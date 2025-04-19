<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'Laravel', 'slug' => 'laravel'],
            ['name' => 'React', 'slug' => 'react'],
            ['name' => 'Vue', 'slug' => 'vue'],
            ['name' => 'Next.js', 'slug' => 'nextjs'],
            ['name' => 'TailwindCSS', 'slug' => 'tailwindcss'],
            ['name' => 'API', 'slug' => 'api'],
            ['name' => 'Responsive', 'slug' => 'responsive'],
            ['name' => 'Mobile', 'slug' => 'mobile'],
            ['name' => 'Docker', 'slug' => 'docker'],
            ['name' => 'AWS', 'slug' => 'aws'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}