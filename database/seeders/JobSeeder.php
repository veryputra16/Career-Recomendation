<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = [
            [
                'company_name' => 'Nusantara Digital Tech',
                'position' => 'Backend Developer Intern',
                'description' => 'Assist in building and maintaining scalable RESTful APIs using Laravel and MySQL. Collaborate with frontend and mobile teams on API integration.',
                'location' => 'Jakarta, Indonesia (Hybrid)',
                'employment_type' => 'Internship',
                'type' => 'internship',
                'source' => 'University Career Portal',
                'source_url' => null,
                'application_url' => null,
                'deadline' => now()->addMonths(2)->format('Y-m-d'),
                'status' => 'active',
            ],
            [
                'company_name' => 'Inovasi Kreasi Asia',
                'position' => 'Frontend Engineer Intern',
                'description' => 'Build modern, interactive web interfaces with React, Tailwind CSS, and Vite. Participate in agile design reviews and component development.',
                'location' => 'Bandung, Indonesia (Remote)',
                'employment_type' => 'Internship',
                'type' => 'internship',
                'source' => 'University Career Portal',
                'source_url' => null,
                'application_url' => null,
                'deadline' => now()->addMonths(3)->format('Y-m-d'),
                'status' => 'active',
            ],
            [
                'company_name' => 'Karya Cloud Solution',
                'position' => 'DevOps & Cloud Engineer Intern',
                'description' => 'Learn and implement automated CI/CD deployment pipelines, containerization using Docker, and basic cloud resource management on AWS.',
                'location' => 'Surabaya, Indonesia (On-site)',
                'employment_type' => 'Internship',
                'type' => 'internship',
                'source' => 'Tech Talent Expo',
                'source_url' => null,
                'application_url' => null,
                'deadline' => now()->addMonths(1)->format('Y-m-d'),
                'status' => 'active',
            ],
            [
                'company_name' => 'Data Cerdas Pratama',
                'position' => 'Junior Data Analyst',
                'description' => 'Analyze business performance metrics, create interactive Tableau/PowerBI dashboards, and run exploratory data queries using SQL and Python.',
                'location' => 'Jakarta, Indonesia (Hybrid)',
                'employment_type' => 'Full-time',
                'type' => 'job',
                'source' => 'Industry Partner Directory',
                'source_url' => null,
                'application_url' => null,
                'deadline' => now()->addMonths(2)->format('Y-m-d'),
                'status' => 'active',
            ],
            [
                'company_name' => 'Global Solusindo Tech',
                'position' => 'Junior Fullstack Web Developer',
                'description' => 'Develop end-to-end web applications with Laravel, Inertia.js, and React. Work across database design, API controllers, and frontend views.',
                'location' => 'Yogyakarta, Indonesia (Hybrid)',
                'employment_type' => 'Full-time',
                'type' => 'job',
                'source' => 'Internal Campus Placement',
                'source_url' => null,
                'application_url' => null,
                'deadline' => now()->addMonths(2)->format('Y-m-d'),
                'status' => 'active',
            ],
            [
                'company_name' => 'Bina Talenta App',
                'position' => 'Junior Mobile Developer',
                'description' => 'Build and publish cross-platform mobile applications using Flutter and Dart. Work closely with product designers and backend engineers.',
                'location' => 'Denpasar, Indonesia (Remote)',
                'employment_type' => 'Full-time',
                'type' => 'job',
                'source' => 'Startup Career Fair',
                'source_url' => null,
                'application_url' => null,
                'deadline' => now()->addMonths(1)->format('Y-m-d'),
                'status' => 'active',
            ],
        ];

        foreach ($jobs as $job) {
            Job::updateOrCreate(
                [
                    'company_name' => $job['company_name'],
                    'position' => $job['position'],
                ],
                $job
            );
        }
    }
}
