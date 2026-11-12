<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            'Communication',
            'Project management',
            'Adaptability',
            'Empathy',
            'Leadership',
            'Business intelligence',
            'Negotiation',
            'Time management',
            'Redux',
            'Webpack',
            'Git',
            'Android',
            'iOS',
            'Flutter',
            'Dart',
            'English',
            'React JS',
            'React Native',
            'Node JS',
            'Maths',
            'Core Java',
            'Manual Testing',
            'Angular JS',
            'JavaScript',
            'C Programming',
            'Aptitude',
            'HTML',
            'PHP',
            'Critical Thinking',
            'Programming Fundamentals'
        ];

        foreach ($skills as $skill) {
            Skill::firstOrCreate(
                ['slug' => Str::slug($skill)],
                ['name' => $skill]
            );
        }
    }
}
