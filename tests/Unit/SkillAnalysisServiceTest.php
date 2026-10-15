<?php

namespace Tests\Unit;

use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\User;
use App\Services\Skill\SkillAnalysisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillAnalysisServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SkillAnalysisService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SkillAnalysisService();
    }

    public function test_analyze_with_null_student_returns_empty_structure(): void
    {
        $result = $this->service->analyze(null);

        $this->assertSame(0, $result['total_skills']);
        $this->assertSame(0, $result['total_categories']);
        $this->assertNull($result['top_category']);
        $this->assertNull($result['average_confidence']);
        $this->assertSame(0, $result['source_breakdown']['cv_ai']);
        $this->assertSame(0, $result['source_breakdown']['manual']);
        $this->assertEmpty($result['categories']);
        $this->assertEmpty($result['skills']);
    }

    public function test_analyze_with_student_having_no_skills_returns_empty_structure(): void
    {
        $user = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        $result = $this->service->analyze($student);

        $this->assertSame(0, $result['total_skills']);
        $this->assertSame(0, $result['total_categories']);
        $this->assertNull($result['top_category']);
        $this->assertEmpty($result['categories']);
        $this->assertEmpty($result['skills']);
    }

    public function test_analyze_aggregates_skills_and_groups_by_category(): void
    {
        $user = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        $php = Skill::create(['name' => 'PHP', 'slug' => 'php', 'category' => 'language', 'is_active' => true]);
        $js = Skill::create(['name' => 'JavaScript', 'slug' => 'javascript', 'category' => 'language', 'is_active' => true]);
        $laravel = Skill::create(['name' => 'Laravel', 'slug' => 'laravel', 'category' => 'framework', 'is_active' => true]);
        $mysql = Skill::create(['name' => 'MySQL', 'slug' => 'mysql', 'category' => 'database', 'is_active' => true]);

        StudentSkill::create([
            'student_id' => $student->id,
            'skill_id' => $php->id,
            'confidence_score' => 0.90,
            'proficiency_level' => 'intermediate',
            'source' => 'cv_ai',
        ]);

        StudentSkill::create([
            'student_id' => $student->id,
            'skill_id' => $js->id,
            'confidence_score' => 0.80,
            'source' => 'cv_ai',
        ]);

        StudentSkill::create([
            'student_id' => $student->id,
            'skill_id' => $laravel->id,
            'confidence_score' => 0.95,
            'source' => 'manual',
        ]);

        StudentSkill::create([
            'student_id' => $student->id,
            'skill_id' => $mysql->id,
            'confidence_score' => 0.85,
            'source' => 'cv_ai',
        ]);

        $result = $this->service->analyze($student);

        $this->assertSame(4, $result['total_skills']);
        $this->assertSame(3, $result['total_categories']); // language, framework, database

        // Check top category (language has 2 skills = 50.0%)
        $this->assertNotNull($result['top_category']);
        $this->assertSame('language', $result['top_category']['key']);
        $this->assertSame('Programming Languages', $result['top_category']['label']);
        $this->assertSame(2, $result['top_category']['count']);
        $this->assertSame(50.0, $result['top_category']['percentage']);

        // Check categories list
        $categories = collect($result['categories']);
        $languageCategory = $categories->firstWhere('key', 'language');
        $this->assertNotNull($languageCategory);
        $this->assertSame(2, $languageCategory['count']);
        $this->assertSame(50.0, $languageCategory['percentage']);
        $this->assertSame(0.85, $languageCategory['average_confidence']);
        $this->assertSame(85, $languageCategory['average_confidence_percentage']);

        $frameworkCategory = $categories->firstWhere('key', 'framework');
        $this->assertNotNull($frameworkCategory);
        $this->assertSame(1, $frameworkCategory['count']);
        $this->assertSame(25.0, $frameworkCategory['percentage']);

        // Check source breakdown
        $this->assertSame(3, $result['source_breakdown']['cv_ai']);
        $this->assertSame(1, $result['source_breakdown']['manual']);
        $this->assertSame(0, $result['source_breakdown']['other']);

        // Check overall confidence (average of 0.90, 0.80, 0.95, 0.85 = 0.88)
        $this->assertSame(0.88, $result['average_confidence']);
        $this->assertSame(88, $result['average_confidence_percentage']);

        // Check individual skill formatting
        $skills = collect($result['skills']);
        $phpSkill = $skills->firstWhere('name', 'PHP');
        $this->assertNotNull($phpSkill);
        $this->assertSame('language', $phpSkill['category']);
        $this->assertSame('Programming Languages', $phpSkill['category_label']);
        $this->assertSame(0.90, $phpSkill['confidence_score']);
        $this->assertSame(90, $phpSkill['confidence_percentage']);
        $this->assertSame('intermediate', $phpSkill['proficiency_level']);
        $this->assertSame('cv_ai', $phpSkill['source']);
        $this->assertSame('CV Extraction (AI)', $phpSkill['source_label']);
    }

    public function test_resolve_category_label_handles_known_and_custom_categories(): void
    {
        $this->assertSame('Programming Languages', $this->service->resolveCategoryLabel('language'));
        $this->assertSame('Frameworks & Libraries', $this->service->resolveCategoryLabel('framework'));
        $this->assertSame('Databases', $this->service->resolveCategoryLabel('database'));
        $this->assertSame('Developer Tools', $this->service->resolveCategoryLabel('tool'));
        $this->assertSame('Cloud & DevOps', $this->service->resolveCategoryLabel('cloud'));
        $this->assertSame('Soft Skills', $this->service->resolveCategoryLabel('soft_skill'));
        $this->assertSame('Data Science', $this->service->resolveCategoryLabel('data_science'));
        $this->assertSame('Uncategorized', $this->service->resolveCategoryLabel(''));
    }
}
