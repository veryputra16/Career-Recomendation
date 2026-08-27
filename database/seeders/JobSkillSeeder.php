<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\JobSkill;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matrix = [
            'Backend Developer Intern' => [
                'required' => ['PHP', 'Laravel', 'MySQL', 'Git'],
                'preferred' => ['Redis', 'Postman', 'Problem Solving'],
            ],
            'Frontend Engineer Intern' => [
                'required' => ['JavaScript', 'React', 'HTML/CSS', 'Tailwind CSS'],
                'preferred' => ['TypeScript', 'Figma', 'Problem Solving'],
            ],
            'DevOps & Cloud Engineer Intern' => [
                'required' => ['Docker', 'Linux / Bash', 'Git'],
                'preferred' => ['AWS', 'CI/CD Pipelines', 'Problem Solving'],
            ],
            'Junior Data Analyst' => [
                'required' => ['SQL', 'Python', 'Pandas / NumPy', 'Tableau / PowerBI'],
                'preferred' => ['PostgreSQL', 'Problem Solving', 'Communication'],
            ],
            'Junior Fullstack Web Developer' => [
                'required' => ['PHP', 'Laravel', 'React', 'JavaScript', 'MySQL', 'Tailwind CSS'],
                'preferred' => ['Inertia.js', 'Git', 'Problem Solving'],
            ],
            'Junior Mobile Developer' => [
                'required' => ['Flutter', 'Dart', 'Git'],
                'preferred' => ['Kotlin', 'Postman', 'Problem Solving'],
            ],
        ];

        foreach ($matrix as $position => $categories) {
            $job = Job::where('position', $position)->first();
            if (! $job) {
                continue;
            }

            foreach ($categories as $importance => $skillNames) {
                foreach ($skillNames as $skillName) {
                    $skill = Skill::where('slug', Str::slug($skillName))->first();
                    if (! $skill) {
                        continue;
                    }

                    JobSkill::updateOrCreate(
                        [
                            'job_id' => $job->id,
                            'skill_id' => $skill->id,
                        ],
                        [
                            'importance' => $importance,
                        ]
                    );
                }
            }
        }
    }
}
