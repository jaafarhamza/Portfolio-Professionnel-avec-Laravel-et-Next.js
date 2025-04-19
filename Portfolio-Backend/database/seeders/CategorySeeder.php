<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Web Development', 'slug' => 'web-development'],
            ['name' => 'Mobile Development', 'slug' => 'mobile-development'],
            ['name' => 'UI/UX Design', 'slug' => 'ui-ux-design'],
            ['name' => 'DevOps', 'slug' => 'devops'],
            ['name' => 'Career', 'slug' => 'career'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}