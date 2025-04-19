<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Article::factory(10)->create()->each(function ($article) {
            // Attach random tags to each article
            $tags = Tag::inRandomOrder()->limit(3)->get();
            $article->tags()->attach($tags);
        });
    }
}