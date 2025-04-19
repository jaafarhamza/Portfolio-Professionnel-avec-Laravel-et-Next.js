<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::factory(5)->create()->each(function ($project) {
            // Attach random skills to each project
            $skills = Skill::inRandomOrder()->limit(3)->get();
            $project->skills()->attach($skills);
        });
    }
}