<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\CareerSkill;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CareerSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matrix = [
            'Backend Developer' => [
                'required' => ['PHP', 'Laravel', 'SQL', 'MySQL', 'Git', 'Problem Solving'],
                'recommended' => ['Docker', 'Redis', 'PostgreSQL', 'Postman', 'Communication'],
                'optional' => ['AWS', 'Linux / Bash'],
            ],
            'Frontend Developer' => [
                'required' => ['JavaScript', 'React', 'HTML/CSS', 'Tailwind CSS', 'Git', 'Problem Solving'],
                'recommended' => ['TypeScript', 'Next.js', 'Inertia.js', 'Figma', 'Communication'],
                'optional' => ['Vue.js'],
            ],
            'Fullstack Developer' => [
                'required' => ['JavaScript', 'React', 'PHP', 'Laravel', 'MySQL', 'HTML/CSS', 'Git', 'Problem Solving'],
                'recommended' => ['TypeScript', 'Docker', 'Redis', 'Tailwind CSS', 'Inertia.js', 'Communication'],
                'optional' => ['AWS', 'Next.js'],
            ],
            'Mobile Developer' => [
                'required' => ['Kotlin', 'Flutter', 'Dart', 'Git', 'Problem Solving'],
                'recommended' => ['Java', 'Postman', 'Communication'],
                'optional' => ['TypeScript'],
            ],
            'Data Analyst' => [
                'required' => ['SQL', 'Python', 'Pandas / NumPy', 'Tableau / PowerBI', 'Problem Solving'],
                'recommended' => ['PostgreSQL', 'Critical Thinking', 'Communication'],
                'optional' => ['MySQL'],
            ],
            'Data Scientist' => [
                'required' => ['Python', 'Pandas / NumPy', 'Scikit-learn', 'SQL', 'Problem Solving'],
                'recommended' => ['TensorFlow / PyTorch', 'PostgreSQL', 'Critical Thinking', 'Communication'],
                'optional' => ['Tableau / PowerBI'],
            ],
            'UI/UX Designer' => [
                'required' => ['Figma', 'HTML/CSS', 'Communication', 'Critical Thinking'],
                'recommended' => ['Problem Solving', 'Tailwind CSS'],
                'optional' => ['React'],
            ],
            'DevOps Engineer' => [
                'required' => ['Docker', 'Kubernetes', 'Linux / Bash', 'CI/CD Pipelines', 'Git', 'Problem Solving'],
                'recommended' => ['AWS', 'Google Cloud', 'Python', 'Communication'],
                'optional' => ['Redis'],
            ],
            'Cloud Engineer' => [
                'required' => ['AWS', 'Google Cloud', 'Docker', 'Linux / Bash', 'Git', 'Problem Solving'],
                'recommended' => ['Kubernetes', 'CI/CD Pipelines', 'Python', 'Communication'],
                'optional' => ['PostgreSQL'],
            ],
            'Cybersecurity Analyst' => [
                'required' => ['Linux / Bash', 'Problem Solving', 'Critical Thinking', 'Communication'],
                'recommended' => ['Python', 'SQL', 'AWS'],
                'optional' => ['Docker'],
            ],
        ];

        foreach ($matrix as $careerName => $categories) {
            $career = Career::where('slug', Str::slug($careerName))->first();
            if (! $career) {
                continue;
            }

            foreach ($categories as $importance => $skillNames) {
                foreach ($skillNames as $skillName) {
                    $skill = Skill::where('slug', Str::slug($skillName))->first();
                    if (! $skill) {
                        continue;
                    }

                    CareerSkill::updateOrCreate(
                        [
                            'career_id' => $career->id,
                            'skill_id' => $skill->id,
                        ],
                        [
                            'importance' => $importance,
                            'minimum_level' => $importance === 'required' ? 'intermediate' : 'beginner',
                        ]
                    );
                }
            }
        }
    }
}
