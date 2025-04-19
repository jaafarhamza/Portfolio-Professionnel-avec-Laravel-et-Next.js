<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            [
                'name' => 'Laravel',
                'slug' => 'laravel',
                'category' => 'Backend',
                'proficiency' => 90,
                'is_highlighted' => true,
                'order' => 1,
            ],
            [
                'name' => 'Next.js',
                'slug' => 'nextjs',
                'category' => 'Frontend',
                'proficiency' => 85,
                'is_highlighted' => true,
                'order' => 2,
            ],
            [
                'name' => 'React',
                'slug' => 'react',
                'category' => 'Frontend',
                'proficiency' => 90,
                'is_highlighted' => true,
                'order' => 3,
            ],
            [
                'name' => 'TailwindCSS',
                'slug' => 'tailwindcss',
                'category' => 'Frontend',
                'proficiency' => 85,
                'is_highlighted' => true,
                'order' => 4,
            ],
            [
                'name' => 'Docker',
                'slug' => 'docker',
                'category' => 'DevOps',
                'proficiency' => 75,
                'is_highlighted' => false,
                'order' => 5,
            ],
            [
                'name' => 'MySQL',
                'slug' => 'mysql',
                'category' => 'Database',
                'proficiency' => 85,
                'is_highlighted' => false,
                'order' => 6,
            ],
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }
    }
}