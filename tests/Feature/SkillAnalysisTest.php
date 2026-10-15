<?php

namespace Tests\Feature;

use App\Models\CVUpload;
use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SkillAnalysisTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Skill::create(['name' => 'PHP', 'slug' => 'php', 'category' => 'language', 'is_active' => true]);
        Skill::create(['name' => 'Laravel', 'slug' => 'laravel', 'category' => 'framework', 'is_active' => true]);
        Skill::create(['name' => 'React', 'slug' => 'react', 'category' => 'framework', 'is_active' => true]);
        Skill::create(['name' => 'MySQL', 'slug' => 'mysql', 'category' => 'database', 'is_active' => true]);
        Skill::create(['name' => 'Git', 'slug' => 'git', 'category' => 'tool', 'is_active' => true]);
    }

    public function test_unauthenticated_user_cannot_access_skill_analysis(): void
    {
        $response = $this->get('/student/skills');

        $response->assertRedirect('/login');
    }

    public function test_admin_cannot_access_student_skill_analysis(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/student/skills');

        $response->assertStatus(403);
    }

    public function test_authenticated_student_can_view_empty_skill_analysis(): void
    {
        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $response = $this->actingAs($studentUser)->get('/student/skills');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Student/Skills/Index')
            ->where('analysis.total_skills', 0)
            ->where('analysis.total_categories', 0)
            ->where('analysis.skills', [])
            ->where('hasCv', false)
            ->where('latestCv', null)
        );
    }

    public function test_authenticated_student_views_own_skills_with_category_grouping(): void
    {
        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $php = Skill::where('slug', 'php')->first();
        $laravel = Skill::where('slug', 'laravel')->first();
        $mysql = Skill::where('slug', 'mysql')->first();

        StudentSkill::create([
            'student_id' => $student->id,
            'skill_id' => $php->id,
            'confidence_score' => 0.95,
            'source' => 'cv_ai',
        ]);

        StudentSkill::create([
            'student_id' => $student->id,
            'skill_id' => $laravel->id,
            'confidence_score' => 0.90,
            'source' => 'cv_ai',
        ]);

        StudentSkill::create([
            'student_id' => $student->id,
            'skill_id' => $mysql->id,
            'confidence_score' => 0.85,
            'source' => 'manual',
        ]);

        $response = $this->actingAs($studentUser)->get('/student/skills');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Student/Skills/Index')
            ->where('analysis.total_skills', 3)
            ->where('analysis.total_categories', 3)
            ->has('analysis.skills', 3)
            ->has('analysis.categories', 3)
            ->where('analysis.source_breakdown.cv_ai', 2)
            ->where('analysis.source_breakdown.manual', 1)
            ->where('analysis.average_confidence_percentage', 90)
        );
    }

    public function test_student_cannot_access_or_view_another_students_skills(): void
    {
        // Student A with skills
        $studentUserA = User::factory()->student()->create();
        $studentA = Student::factory()->create(['user_id' => $studentUserA->id]);

        $php = Skill::where('slug', 'php')->first();
        StudentSkill::create([
            'student_id' => $studentA->id,
            'skill_id' => $php->id,
            'confidence_score' => 0.95,
            'source' => 'cv_ai',
        ]);

        // Student B with NO skills
        $studentUserB = User::factory()->student()->create();
        $studentB = Student::factory()->create(['user_id' => $studentUserB->id]);

        // Student B accesses the skill page
        $response = $this->actingAs($studentUserB)->get('/student/skills');

        $response->assertStatus(200);
        // Student B MUST NOT see Student A's skills
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Student/Skills/Index')
            ->where('analysis.total_skills', 0)
            ->where('analysis.skills', [])
        );
    }

    public function test_student_with_uploaded_cv_receives_cv_context_flag(): void
    {
        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        CVUpload::create([
            'student_id' => $student->id,
            'original_filename' => 'john_doe_resume.pdf',
            'stored_filename' => 'resume.pdf',
            'file_path' => 'cv/resume.pdf',
            'file_size' => 2048,
            'mime_type' => 'application/pdf',
            'status' => 'processed',
            'extracted_text' => 'Sample CV text with PHP and Laravel',
        ]);

        $response = $this->actingAs($studentUser)->get('/student/skills');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Student/Skills/Index')
            ->where('hasCv', true)
            ->where('latestCv.filename', 'john_doe_resume.pdf')
            ->where('latestCv.has_extracted_text', true)
        );
    }
}
