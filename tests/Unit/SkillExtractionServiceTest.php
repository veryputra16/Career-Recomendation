<?php

namespace Tests\Unit;

use App\Models\CVAnalysisHistory;
use App\Models\CVUpload;
use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\User;
use App\Services\AI\AIService;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\SkillExtractionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class SkillExtractionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic skills for testing
        Skill::create(['name' => 'PHP', 'slug' => 'php', 'category' => 'language', 'is_active' => true]);
        Skill::create(['name' => 'Laravel', 'slug' => 'laravel', 'category' => 'framework', 'is_active' => true]);
        Skill::create(['name' => 'React', 'slug' => 'react', 'category' => 'framework', 'is_active' => true]);
        Skill::create(['name' => 'MySQL', 'slug' => 'mysql', 'category' => 'database', 'is_active' => true]);
        Skill::create(['name' => 'Docker', 'slug' => 'docker', 'category' => 'tool', 'is_active' => true]);
        Skill::create(['name' => 'Problem Solving', 'slug' => 'problem-solving', 'category' => 'soft_skill', 'is_active' => true]);
    }

    public function test_parse_and_validate_response_parses_valid_json(): void
    {
        $mockProvider = Mockery::mock(AIProviderInterface::class);
        $aiService = new AIService($mockProvider);
        $service = new SkillExtractionService($aiService);

        $json = json_encode([
            'skills' => [
                ['name' => 'PHP', 'confidence' => 0.95],
                ['name' => 'Laravel', 'confidence' => 0.88],
            ],
        ]);

        $result = $service->parseAndValidateResponse($json);

        $this->assertCount(2, $result['skills']);
        $this->assertEquals('PHP', $result['skills'][0]['name']);
        $this->assertEquals(0.95, $result['skills'][0]['confidence']);
    }

    public function test_parse_and_validate_response_handles_markdown_fences(): void
    {
        $mockProvider = Mockery::mock(AIProviderInterface::class);
        $aiService = new AIService($mockProvider);
        $service = new SkillExtractionService($aiService);

        $json = "```json\n" . json_encode([
            'skills' => [
                ['name' => 'Docker', 'confidence' => 0.75],
            ],
        ]) . "\n```";

        $result = $service->parseAndValidateResponse($json);

        $this->assertCount(1, $result['skills']);
        $this->assertEquals('Docker', $result['skills'][0]['name']);
    }

    public function test_parse_and_validate_response_throws_on_invalid_json(): void
    {
        $mockProvider = Mockery::mock(AIProviderInterface::class);
        $aiService = new AIService($mockProvider);
        $service = new SkillExtractionService($aiService);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The AI provider returned malformed JSON');

        $service->parseAndValidateResponse('Not a json payload');
    }

    public function test_parse_and_validate_response_throws_when_skills_key_is_missing(): void
    {
        $mockProvider = Mockery::mock(AIProviderInterface::class);
        $aiService = new AIService($mockProvider);
        $service = new SkillExtractionService($aiService);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing or invalid "skills" list in JSON payload');

        $service->parseAndValidateResponse(json_encode(['unrelated_key' => 'value']));
    }

    public function test_match_skills_matches_exact_and_alias_names(): void
    {
        $mockProvider = Mockery::mock(AIProviderInterface::class);
        $aiService = new AIService($mockProvider);
        $service = new SkillExtractionService($aiService);

        $rawSkills = [
            ['name' => 'php', 'confidence' => 0.98], // lowercase match
            ['name' => 'Laravel Framework', 'confidence' => 0.92], // suffix noise match
            ['name' => 'ReactJS', 'confidence' => 0.85], // alias map match
            ['name' => 'Unknown Exotic Library', 'confidence' => 0.60], // unmatched
        ];

        $result = $service->matchSkills($rawSkills);

        $this->assertCount(3, $result['matched']);
        $this->assertCount(1, $result['unmatched']);

        $matchedNames = array_map(fn ($m) => $m['skill']->name, $result['matched']);
        $this->assertContains('PHP', $matchedNames);
        $this->assertContains('Laravel', $matchedNames);
        $this->assertContains('React', $matchedNames);

        $this->assertEquals('Unknown Exotic Library', $result['unmatched'][0]['name']);
    }

    public function test_extract_skills_persists_to_database_with_mocked_ai(): void
    {
        $user = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        $cvUpload = CVUpload::create([
            'student_id' => $student->id,
            'original_filename' => 'cv.pdf',
            'stored_filename' => 'cv.pdf',
            'file_path' => 'cv/cv.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'status' => 'processed',
            'extracted_text' => 'Experienced PHP and Laravel web developer with MySQL database skills.',
            'uploaded_at' => now(),
            'processed_at' => now(),
        ]);

        $mockResponse = json_encode([
            'skills' => [
                ['name' => 'PHP', 'confidence' => 0.95],
                ['name' => 'Laravel', 'confidence' => 0.90],
                ['name' => 'MySQL', 'confidence' => 0.85],
            ],
        ]);

        $mockProvider = Mockery::mock(AIProviderInterface::class);
        $mockProvider->shouldReceive('generateText')
            ->once()
            ->andReturn($mockResponse);

        $aiService = new AIService($mockProvider);
        $service = new SkillExtractionService($aiService);

        $result = $service->extractSkills($student, $cvUpload);

        $this->assertTrue($result['success']);
        $this->assertEquals(3, $result['matched_count']);
        $this->assertEquals(0, $result['unmatched_count']);

        // Check student_skills
        $this->assertEquals(3, StudentSkill::where('student_id', $student->id)->count());
        $this->assertDatabaseHas('student_skills', [
            'student_id' => $student->id,
            'source' => 'cv_ai',
        ]);

        // Check cv_analysis_histories
        $this->assertDatabaseHas('cv_analysis_histories', [
            'student_id' => $student->id,
            'cv_upload_id' => $cvUpload->id,
            'status' => 'completed',
        ]);
    }

    public function test_extract_skills_handles_ai_failure_and_records_failed_history(): void
    {
        $user = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        $cvUpload = CVUpload::create([
            'student_id' => $student->id,
            'original_filename' => 'cv.pdf',
            'stored_filename' => 'cv.pdf',
            'file_path' => 'cv/cv.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'status' => 'processed',
            'extracted_text' => 'Software developer resume text',
            'uploaded_at' => now(),
            'processed_at' => now(),
        ]);

        $mockProvider = Mockery::mock(AIProviderInterface::class);
        $mockProvider->shouldReceive('generateText')
            ->once()
            ->andThrow(new RuntimeException('API Rate Limit Exceeded'));

        $aiService = new AIService($mockProvider);
        $service = new SkillExtractionService($aiService);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('AI skill extraction could not be completed: API Rate Limit Exceeded');

        try {
            $service->extractSkills($student, $cvUpload);
        } finally {
            $this->assertDatabaseHas('cv_analysis_histories', [
                'student_id' => $student->id,
                'cv_upload_id' => $cvUpload->id,
                'status' => 'failed',
            ]);
        }
    }
}
