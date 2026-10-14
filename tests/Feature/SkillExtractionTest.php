<?php

namespace Tests\Feature;

use App\Models\CVAnalysisHistory;
use App\Models\CVUpload;
use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\User;
use App\Services\AI\AIService;
use App\Services\AI\Contracts\AIProviderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class SkillExtractionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Skill::create(['name' => 'PHP', 'slug' => 'php', 'category' => 'language', 'is_active' => true]);
        Skill::create(['name' => 'Laravel', 'slug' => 'laravel', 'category' => 'framework', 'is_active' => true]);
        Skill::create(['name' => 'React', 'slug' => 'react', 'category' => 'framework', 'is_active' => true]);
        Skill::create(['name' => 'MySQL', 'slug' => 'mysql', 'category' => 'database', 'is_active' => true]);
        Skill::create(['name' => 'Docker', 'slug' => 'docker', 'category' => 'tool', 'is_active' => true]);
    }

    public function test_unauthenticated_user_cannot_trigger_skill_extraction(): void
    {
        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $cvUpload = CVUpload::create([
            'student_id' => $student->id,
            'original_filename' => 'resume.pdf',
            'stored_filename' => 'resume.pdf',
            'file_path' => 'cv/resume.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'status' => 'processed',
            'extracted_text' => 'Sample CV text with PHP and Laravel',
        ]);

        $response = $this->post("/student/cv/{$cvUpload->id}/extract-skills");

        $response->assertRedirect('/login');
    }

    public function test_admin_cannot_trigger_student_skill_extraction(): void
    {
        $admin = User::factory()->admin()->create();

        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $cvUpload = CVUpload::create([
            'student_id' => $student->id,
            'original_filename' => 'resume.pdf',
            'stored_filename' => 'resume.pdf',
            'file_path' => 'cv/resume.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'status' => 'processed',
            'extracted_text' => 'Sample CV text with PHP and Laravel',
        ]);

        $response = $this->actingAs($admin)->post("/student/cv/{$cvUpload->id}/extract-skills");

        $response->assertStatus(403);
    }

    public function test_student_cannot_extract_skills_from_another_students_cv(): void
    {
        $studentAUser = User::factory()->student()->create();
        Student::factory()->create(['user_id' => $studentAUser->id]);

        $studentBUser = User::factory()->student()->create();
        $studentB = Student::factory()->create(['user_id' => $studentBUser->id]);

        $cvUploadB = CVUpload::create([
            'student_id' => $studentB->id,
            'original_filename' => 'b_resume.pdf',
            'stored_filename' => 'b_resume.pdf',
            'file_path' => 'cv/b_resume.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'status' => 'processed',
            'extracted_text' => 'Sample CV text',
        ]);

        $response = $this->actingAs($studentAUser)->post("/student/cv/{$cvUploadB->id}/extract-skills");

        $response->assertStatus(403);
    }

    public function test_student_cannot_extract_skills_from_cv_without_extracted_text(): void
    {
        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $cvUpload = CVUpload::create([
            'student_id' => $student->id,
            'original_filename' => 'empty.pdf',
            'stored_filename' => 'empty.pdf',
            'file_path' => 'cv/empty.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'status' => 'failed',
            'extracted_text' => null,
        ]);

        $response = $this->actingAs($studentUser)->post("/student/cv/{$cvUpload->id}/extract-skills");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_student_can_extract_skills_from_own_cv_with_mocked_ai(): void
    {
        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $cvUpload = CVUpload::create([
            'student_id' => $student->id,
            'original_filename' => 'resume.pdf',
            'stored_filename' => 'resume.pdf',
            'file_path' => 'cv/resume.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'status' => 'processed',
            'extracted_text' => 'Senior Developer skilled in Laravel Framework, PHP, and MySQL.',
        ]);

        $mockResponse = json_encode([
            'skills' => [
                ['name' => 'Laravel Framework', 'confidence' => 0.95],
                ['name' => 'PHP', 'confidence' => 0.98],
                ['name' => 'MySQL', 'confidence' => 0.88],
            ],
        ]);

        $mockProvider = Mockery::mock(AIProviderInterface::class);
        $mockProvider->shouldReceive('generateText')
            ->once()
            ->andReturn($mockResponse);

        $this->app->instance(AIService::class, new AIService($mockProvider));

        $response = $this->actingAs($studentUser)->post("/student/cv/{$cvUpload->id}/extract-skills");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify Student Skills are created
        $this->assertEquals(3, StudentSkill::where('student_id', $student->id)->count());
        $this->assertDatabaseHas('student_skills', [
            'student_id' => $student->id,
            'source' => 'cv_ai',
        ]);

        // Verify CV Analysis History is created
        $this->assertDatabaseHas('cv_analysis_histories', [
            'student_id' => $student->id,
            'cv_upload_id' => $cvUpload->id,
            'status' => 'completed',
        ]);
    }

    public function test_re_extraction_updates_skills_without_creating_duplicates(): void
    {
        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $cvUpload = CVUpload::create([
            'student_id' => $student->id,
            'original_filename' => 'resume.pdf',
            'stored_filename' => 'resume.pdf',
            'file_path' => 'cv/resume.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'status' => 'processed',
            'extracted_text' => 'Software Engineer with PHP experience.',
        ]);

        $mockResponse1 = json_encode([
            'skills' => [
                ['name' => 'PHP', 'confidence' => 0.80],
            ],
        ]);

        $mockResponse2 = json_encode([
            'skills' => [
                ['name' => 'PHP', 'confidence' => 0.95],
                ['name' => 'Laravel', 'confidence' => 0.90],
            ],
        ]);

        $mockProvider = Mockery::mock(AIProviderInterface::class);
        $mockProvider->shouldReceive('generateText')
            ->twice()
            ->andReturn($mockResponse1, $mockResponse2);

        $this->app->instance(AIService::class, new AIService($mockProvider));

        // First run
        $this->actingAs($studentUser)->post("/student/cv/{$cvUpload->id}/extract-skills");
        $this->assertEquals(1, StudentSkill::where('student_id', $student->id)->count());

        // Re-extract
        $this->actingAs($studentUser)->post("/student/cv/{$cvUpload->id}/extract-skills");

        // Should have 2 skills (PHP updated to 0.95, Laravel added to 0.90), no duplicate PHP
        $this->assertEquals(2, StudentSkill::where('student_id', $student->id)->count());

        $phpSkill = Skill::where('slug', 'php')->first();
        $this->assertDatabaseHas('student_skills', [
            'student_id' => $student->id,
            'skill_id' => $phpSkill->id,
            'confidence_score' => 0.95,
        ]);
    }

    public function test_skill_extraction_handles_provider_exception_gracefully(): void
    {
        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $cvUpload = CVUpload::create([
            'student_id' => $student->id,
            'original_filename' => 'resume.pdf',
            'stored_filename' => 'resume.pdf',
            'file_path' => 'cv/resume.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'status' => 'processed',
            'extracted_text' => 'Software Engineer resume.',
        ]);

        $mockProvider = Mockery::mock(AIProviderInterface::class);
        $mockProvider->shouldReceive('generateText')
            ->once()
            ->andThrow(new RuntimeException('AI service timeout after 30 seconds'));

        $this->app->instance(AIService::class, new AIService($mockProvider));

        $response = $this->actingAs($studentUser)->post("/student/cv/{$cvUpload->id}/extract-skills");

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('cv_analysis_histories', [
            'student_id' => $student->id,
            'cv_upload_id' => $cvUpload->id,
            'status' => 'failed',
        ]);
    }

    public function test_student_dashboard_reflects_updated_skill_count(): void
    {
        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        $php = Skill::where('slug', 'php')->first();
        StudentSkill::create([
            'student_id' => $student->id,
            'skill_id' => $php->id,
            'confidence_score' => 0.95,
            'source' => 'cv_ai',
        ]);

        $response = $this->actingAs($studentUser)->get('/student');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Student/Dashboard')
            ->has('stats')
            ->where('stats.skills_count', 1)
        );
    }
}
