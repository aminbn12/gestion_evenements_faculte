<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            ['name' => 'PHP', 'slug' => 'php', 'category' => 'technical'],
            ['name' => 'Laravel', 'slug' => 'laravel', 'category' => 'technical'],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'category' => 'technical'],
            ['name' => 'Vue.js', 'slug' => 'vuejs', 'category' => 'technical'],
            ['name' => 'React', 'slug' => 'react', 'category' => 'technical'],
            ['name' => 'Python', 'slug' => 'python', 'category' => 'technical'],
            ['name' => 'Django', 'slug' => 'django', 'category' => 'technical'],
            ['name' => 'MySQL', 'slug' => 'mysql', 'category' => 'technical'],
            ['name' => 'PostgreSQL', 'slug' => 'postgresql', 'category' => 'technical'],
            ['name' => 'Docker', 'slug' => 'docker', 'category' => 'technical'],
            ['name' => 'Git', 'slug' => 'git', 'category' => 'technical'],
            ['name' => 'Linux', 'slug' => 'linux', 'category' => 'technical'],
            ['name' => 'Communication', 'slug' => 'communication', 'category' => 'soft'],
            ['name' => 'Leadership', 'slug' => 'leadership', 'category' => 'soft'],
            ['name' => 'Teamwork', 'slug' => 'teamwork', 'category' => 'soft'],
            ['name' => 'Problem Solving', 'slug' => 'problem-solving', 'category' => 'soft'],
            ['name' => 'Project Management', 'slug' => 'project-management', 'category' => 'soft'],
            ['name' => 'French', 'slug' => 'french', 'category' => 'language'],
            ['name' => 'English', 'slug' => 'english', 'category' => 'language'],
            ['name' => 'Arabic', 'slug' => 'arabic', 'category' => 'language'],
            ['name' => 'Spanish', 'slug' => 'spanish', 'category' => 'language'],
            ['name' => 'Research Methodology', 'slug' => 'research-methodology', 'category' => 'research'],
            ['name' => 'Data Analysis', 'slug' => 'data-analysis', 'category' => 'research'],
            ['name' => 'Academic Writing', 'slug' => 'academic-writing', 'category' => 'research'],
        ];

        foreach ($skills as $skill) {
                Skill::create($skill);
            }
    }
}
