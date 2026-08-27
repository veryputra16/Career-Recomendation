<?php

namespace Database\Seeders;

use App\Models\Career;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CareerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $careers = [
            [
                'name' => 'Backend Developer',
                'category' => 'Software Engineering',
                'description' => 'Designs, builds, and maintains server-side logic, database interactions, RESTful APIs, and application architecture.',
            ],
            [
                'name' => 'Frontend Developer',
                'category' => 'Software Engineering',
                'description' => 'Develops engaging, responsive, and accessible user interfaces and web applications using modern client-side technologies.',
            ],
            [
                'name' => 'Fullstack Developer',
                'category' => 'Software Engineering',
                'description' => 'Possesses expertise across the complete software stack, from interactive client UIs to scalable server architecture and databases.',
            ],
            [
                'name' => 'Mobile Developer',
                'category' => 'Software Engineering',
                'description' => 'Develops performant and user-friendly mobile applications for Android and iOS operating systems.',
            ],
            [
                'name' => 'Data Analyst',
                'category' => 'Data & AI',
                'description' => 'Inspects, cleans, transforms, and models data to discover useful information, inform conclusions, and support business decision-making.',
            ],
            [
                'name' => 'Data Scientist',
                'category' => 'Data & AI',
                'description' => 'Applies scientific methods, machine learning algorithms, and predictive systems to extract knowledge and insights from structured and unstructured data.',
            ],
            [
                'name' => 'UI/UX Designer',
                'category' => 'Design',
                'description' => 'Researches user needs, designs user journey flows, wireframes, high-fidelity prototypes, and user interfaces that maximize product usability.',
            ],
            [
                'name' => 'DevOps Engineer',
                'category' => 'Infrastructure',
                'description' => 'Bridges software development and IT operations through automation, CI/CD pipeline building, monitoring, and infrastructure reliability.',
            ],
            [
                'name' => 'Cloud Engineer',
                'category' => 'Infrastructure',
                'description' => 'Designs, implements, and oversees cloud computing systems, infrastructure architecture, security, and cloud migrations.',
            ],
            [
                'name' => 'Cybersecurity Analyst',
                'category' => 'Security',
                'description' => 'Protects computer systems, networks, and data from cyber attacks, security breaches, and unauthorized digital intrusions.',
            ],
        ];

        foreach ($careers as $career) {
            Career::updateOrCreate(
                ['slug' => Str::slug($career['name'])],
                [
                    'name' => $career['name'],
                    'category' => $career['category'],
                    'description' => $career['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
