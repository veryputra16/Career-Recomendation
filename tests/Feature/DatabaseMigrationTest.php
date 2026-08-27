<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\Job;
use App\Models\Skill;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_master_data_and_demo_users(): void
    {
        $this->seed(DatabaseSeeder::class);

        // Verify Users
        $this->assertDatabaseHas('users', ['email' => 'admin@example.com', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'student@example.com', 'role' => 'student']);

        // Verify Student Profile
        $studentUser = User::where('email', 'student@example.com')->first();
        $this->assertNotNull($studentUser->student);
        $this->assertEquals('Budi Santoso', $studentUser->student->full_name);
        $this->assertGreaterThanOrEqual(1, $studentUser->student->skills()->count());

        // Verify Master Skills
        $this->assertGreaterThanOrEqual(20, Skill::count());
        $this->assertDatabaseHas('skills', ['name' => 'Laravel', 'category' => 'framework']);
        $this->assertDatabaseHas('skills', ['name' => 'React', 'category' => 'framework']);

        // Verify Master Careers
        $this->assertGreaterThanOrEqual(10, Career::count());
        $this->assertDatabaseHas('careers', ['name' => 'Backend Developer']);
        $this->assertDatabaseHas('careers', ['name' => 'Frontend Developer']);

        // Verify Master Jobs & Internships
        $this->assertGreaterThanOrEqual(5, Job::count());
        $this->assertDatabaseHas('jobs', ['type' => 'internship']);
        $this->assertDatabaseHas('jobs', ['type' => 'job']);

        // Verify Career Skills mappings
        $backendCareer = Career::where('slug', 'backend-developer')->first();
        $this->assertGreaterThan(0, $backendCareer->skills()->count());
    }
}
