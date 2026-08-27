<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Master Data
        $this->call([
            SkillSeeder::class,
            CareerSeeder::class,
            CareerSkillSeeder::class,
            JobSeeder::class,
            JobSkillSeeder::class,
        ]);

        // 2. Seed Initial Admin Account for Development
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 3. Seed Initial Demo Student Account & Profile for Development
        $studentUser = User::updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Demo Student',
                'password' => Hash::make('password'),
                'role' => 'student',
                'email_verified_at' => now(),
            ]
        );

        $student = Student::updateOrCreate(
            ['user_id' => $studentUser->id],
            [
                'student_number' => 'STD2026001',
                'full_name' => 'Budi Santoso',
                'university' => 'Universitas Indonesia',
                'faculty' => 'Fakultas Ilmu Komputer',
                'major' => 'Informatika',
                'semester' => 6,
                'graduation_year' => 2026,
                'bio' => 'Third-year computer science student passionate about backend engineering, cloud architecture, and modern web applications.',
            ]
        );

        // Attach sample skills to demo student
        $initialSkills = ['PHP', 'Laravel', 'React', 'JavaScript', 'MySQL', 'Git'];
        foreach ($initialSkills as $skillName) {
            $skill = Skill::where('slug', Str::slug($skillName))->first();
            if ($skill && ! $student->skills()->where('skill_id', $skill->id)->exists()) {
                $student->skills()->attach($skill->id, [
                    'confidence_score' => 0.90,
                    'proficiency_level' => 'intermediate',
                    'source' => 'manual',
                ]);
            }
        }
    }
}
