<?php

namespace Tests\Unit;

use App\Models\Career;
use App\Models\CareerRecommendation;
use App\Models\CVAnalysisHistory;
use App\Models\CVUpload;
use App\Models\Job;
use App\Models\JobMatch;
use App\Models\Report;
use App\Models\Skill;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_one_student_relationship(): void
    {
        $user = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->student->is($student));
        $this->assertTrue($student->user->is($user));
    }

    public function test_student_and_skill_many_to_many_relationship(): void
    {
        $student = Student::factory()->create();
        $skill = Skill::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);

        $student->skills()->attach($skill->id, [
            'confidence_score' => 0.95,
            'proficiency_level' => 'advanced',
            'source' => 'cv_ai',
        ]);

        $this->assertCount(1, $student->skills);
        $this->assertEquals('Laravel', $student->skills->first()->name);
        $this->assertEquals(0.95, $student->skills->first()->pivot->confidence_score);
        $this->assertEquals('advanced', $student->skills->first()->pivot->proficiency_level);
        $this->assertEquals('cv_ai', $student->skills->first()->pivot->source);

        $this->assertCount(1, $skill->students);
        $this->assertTrue($skill->students->first()->is($student));
    }

    public function test_career_and_skill_many_to_many_relationship(): void
    {
        $career = Career::factory()->create(['name' => 'Backend Developer', 'slug' => 'backend-developer']);
        $skill = Skill::factory()->create(['name' => 'PHP', 'slug' => 'php']);

        $career->skills()->attach($skill->id, [
            'importance' => 'required',
            'minimum_level' => 'intermediate',
        ]);

        $this->assertCount(1, $career->skills);
        $this->assertEquals('PHP', $career->skills->first()->name);
        $this->assertEquals('required', $career->skills->first()->pivot->importance);
        $this->assertEquals('intermediate', $career->skills->first()->pivot->minimum_level);

        $this->assertCount(1, $skill->careers);
        $this->assertTrue($skill->careers->first()->is($career));
    }

    public function test_job_and_skill_many_to_many_relationship(): void
    {
        $job = Job::factory()->create(['position' => 'Backend Intern']);
        $skill = Skill::factory()->create(['name' => 'MySQL', 'slug' => 'mysql']);

        $job->skills()->attach($skill->id, [
            'importance' => 'required',
        ]);

        $this->assertCount(1, $job->skills);
        $this->assertEquals('MySQL', $job->skills->first()->name);
        $this->assertEquals('required', $job->skills->first()->pivot->importance);

        $this->assertCount(1, $skill->jobs);
        $this->assertTrue($skill->jobs->first()->is($job));
    }

    public function test_cv_upload_and_analysis_history_lifecycle(): void
    {
        $student = Student::factory()->create();
        $cvUpload = CVUpload::factory()->create(['student_id' => $student->id]);

        $analysis = CVAnalysisHistory::create([
            'student_id' => $student->id,
            'cv_upload_id' => $cvUpload->id,
            'analysis_version' => '1.0',
            'ai_provider' => 'gemini',
            'ai_model' => 'gemini-1.5-pro',
            'prompt_version' => 'v1',
            'status' => 'completed',
            'summary' => 'Strong backend profile.',
            'raw_result' => ['skills' => ['Laravel', 'PHP']],
            'analyzed_at' => now(),
        ]);

        $this->assertTrue($student->cvUploads->first()->is($cvUpload));
        $this->assertTrue($student->cvAnalysisHistories->first()->is($analysis));
        $this->assertTrue($analysis->cvUpload->is($cvUpload));
    }

    public function test_career_recommendation_and_job_matches_relationships(): void
    {
        $student = Student::factory()->create();
        $career = Career::factory()->create();
        $job = Job::factory()->create();

        $recommendation = CareerRecommendation::create([
            'student_id' => $student->id,
            'career_id' => $career->id,
            'matching_score' => 88.50,
            'reason' => 'Good skill coverage for backend development.',
            'existing_skills' => ['PHP', 'Laravel'],
            'missing_skills' => ['Docker'],
            'suggestions' => ['Learn Docker containers.'],
            'recommendation_source' => 'hybrid',
        ]);

        $jobMatch = JobMatch::create([
            'student_id' => $student->id,
            'job_id' => $job->id,
            'matching_score' => 92.00,
            'matched_skills' => ['PHP', 'Laravel', 'MySQL'],
            'missing_skills' => [],
            'explanation' => 'Candidate meets all required criteria.',
            'matching_method' => 'hybrid',
        ]);

        $this->assertTrue($student->careerRecommendations->first()->is($recommendation));
        $this->assertTrue($recommendation->career->is($career));

        $this->assertTrue($student->jobMatches->first()->is($jobMatch));
        $this->assertTrue($jobMatch->job->is($job));
    }

    public function test_report_relationship(): void
    {
        $student = Student::factory()->create();
        $report = Report::create([
            'student_id' => $student->id,
            'file_path' => 'reports/career_report_01.pdf',
            'file_name' => 'career_report_01.pdf',
            'report_type' => 'career_report',
            'generated_at' => now(),
        ]);

        $this->assertTrue($student->reports->first()->is($report));
        $this->assertTrue($report->student->is($student));
    }

    public function test_cascading_delete_on_student(): void
    {
        $student = Student::factory()->create();
        $skill = Skill::factory()->create();
        $student->skills()->attach($skill->id);

        $career = Career::factory()->create();
        CareerRecommendation::create([
            'student_id' => $student->id,
            'career_id' => $career->id,
            'matching_score' => 85.00,
        ]);

        $studentId = $student->id;
        $student->delete();

        $this->assertDatabaseMissing('students', ['id' => $studentId]);
        $this->assertDatabaseMissing('student_skills', ['student_id' => $studentId]);
        $this->assertDatabaseMissing('career_recommendations', ['student_id' => $studentId]);
    }
}
