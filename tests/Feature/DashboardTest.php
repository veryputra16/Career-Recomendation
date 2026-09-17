<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\Job;
use App\Models\Skill;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_student_dashboard(): void
    {
        $response = $this->get('/student');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_admin_dashboard_with_counts(): void
    {
        $admin = User::factory()->admin()->create();

        // Create sample records
        Student::factory()->count(3)->create();
        Skill::factory()->count(5)->create();
        Career::factory()->count(2)->create();
        Job::factory()->count(4)->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Dashboard')
            ->has('counts')
            ->where('counts.students', 3)
            ->where('counts.skills', 5)
            ->where('counts.careers', 2)
            ->where('counts.jobs', 4)
        );
    }

    public function test_student_can_access_student_dashboard_with_stats(): void
    {
        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create([
            'user_id' => $studentUser->id,
            'full_name' => 'John Doe',
            'student_number' => 'STU-12345',
            'major' => 'Computer Science',
        ]);

        $response = $this->actingAs($studentUser)->get('/student');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Student/Dashboard')
            ->has('stats')
            ->where('stats.has_cv', false)
            ->where('stats.cv_count', 0)
            ->where('stats.skills_count', 0)
            ->where('stats.recommendations_count', 0)
            ->where('stats.job_matches_count', 0)
        );
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $studentUser = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $studentUser->id]);

        $response = $this->actingAs($studentUser)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_student_dashboard(): void
    {
        $adminUser = User::factory()->admin()->create();

        $response = $this->actingAs($adminUser)->get('/student');

        $response->assertStatus(403);
    }
}
