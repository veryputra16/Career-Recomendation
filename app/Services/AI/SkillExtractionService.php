<?php

namespace App\Services\AI;

use App\Models\CVAnalysisHistory;
use App\Models\CVUpload;
use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentSkill;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JsonException;
use RuntimeException;
use Throwable;

class SkillExtractionService
{
    public const PROMPT_VERSION = 'skill-extraction-v1';
    public const MAX_CV_TEXT_LENGTH = 10000;

    /**
     * Controlled synonym / alias mappings for normalizing common AI variations.
     * Maps normalized lowercase variant string => official skill name / slug.
     *
     * @var array<string, string>
     */
    protected array $aliasMap = [
        'reactjs' => 'React',
        'react.js' => 'React',
        'react js' => 'React',
        'vuejs' => 'Vue.js',
        'vue.js' => 'Vue.js',
        'vue js' => 'Vue.js',
        'nextjs' => 'Next.js',
        'next.js' => 'Next.js',
        'next js' => 'Next.js',
        'express' => 'Express.js',
        'expressjs' => 'Express.js',
        'express.js' => 'Express.js',
        'spring' => 'Spring Boot',
        'springboot' => 'Spring Boot',
        'fast api' => 'FastAPI',
        'flutter framework' => 'Flutter',
        'tailwind' => 'Tailwind CSS',
        'tailwindcss' => 'Tailwind CSS',
        'tailwind-css' => 'Tailwind CSS',
        'inertia' => 'Inertia.js',
        'inertiajs' => 'Inertia.js',
        'postgres' => 'PostgreSQL',
        'postgresql database' => 'PostgreSQL',
        'mysql database' => 'MySQL',
        'mongo' => 'MongoDB',
        'redis cache' => 'Redis',
        'docker container' => 'Docker',
        'docker compose' => 'Docker',
        'k8s' => 'Kubernetes',
        'git version control' => 'Git',
        'github' => 'Git',
        'gitlab' => 'Git',
        'amazon web services' => 'AWS',
        'gcp' => 'Google Cloud',
        'google cloud platform' => 'Google Cloud',
        'ci/cd' => 'CI/CD Pipelines',
        'cicd' => 'CI/CD Pipelines',
        'github actions' => 'CI/CD Pipelines',
        'gitlab ci' => 'CI/CD Pipelines',
        'linux' => 'Linux / Bash',
        'bash' => 'Linux / Bash',
        'shell scripting' => 'Linux / Bash',
        'postman api' => 'Postman',
        'pandas' => 'Pandas / NumPy',
        'numpy' => 'Pandas / NumPy',
        'scikit learn' => 'Scikit-learn',
        'sklearn' => 'Scikit-learn',
        'tensorflow' => 'TensorFlow / PyTorch',
        'pytorch' => 'TensorFlow / PyTorch',
        'powerbi' => 'Tableau / PowerBI',
        'power bi' => 'Tableau / PowerBI',
        'tableau' => 'Tableau / PowerBI',
        'html' => 'HTML/CSS',
        'css' => 'HTML/CSS',
        'html5' => 'HTML/CSS',
        'css3' => 'HTML/CSS',
        'laravel framework' => 'Laravel',
        'php laravel' => 'Laravel',
        'node' => 'JavaScript',
        'nodejs' => 'JavaScript',
        'node.js' => 'JavaScript',
        'problem-solving' => 'Problem Solving',
        'leadership' => 'Team Leadership',
        'team player' => 'Communication',
    ];

    public function __construct(
        protected AIService $aiService
    ) {}

    /**
     * Extract skills from a student's processed CV, match against catalog,
     * persist to student_skills, and log to cv_analysis_histories.
     *
     * @param Student $student
     * @param CVUpload $cvUpload
     * @return array<string, mixed>
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function extractSkills(Student $student, CVUpload $cvUpload): array
    {
        if ($cvUpload->student_id !== $student->id) {
            throw new InvalidArgumentException('Unauthorized: CV document does not belong to this student.');
        }

        $rawText = trim($cvUpload->extracted_text ?? '');
        if ($rawText === '') {
            throw new InvalidArgumentException('The selected CV has no readable text. Please re-upload a selectable PDF document.');
        }

        // Limit length to avoid token waste
        $cvText = mb_substr($rawText, 0, self::MAX_CV_TEXT_LENGTH);

        $prompt = $this->buildPrompt($cvText);

        $analysisVersion = (string) ($student->cvAnalysisHistories()->count() + 1);

        Log::info('Initiating AI skill extraction', [
            'student_id' => $student->id,
            'cv_upload_id' => $cvUpload->id,
            'version' => $analysisVersion,
        ]);

        try {
            $aiResponse = $this->aiService->executePrompt($prompt, [
                'json_mode' => true,
            ]);

            $parsedData = $this->parseAndValidateResponse($aiResponse);
            $rawSkills = $parsedData['skills'];

            $matchingResult = $this->matchSkills($rawSkills);

            $savedCount = 0;
            DB::transaction(function () use ($student, $matchingResult, &$savedCount) {
                foreach ($matchingResult['matched'] as $match) {
                    /** @var Skill $skill */
                    $skill = $match['skill'];
                    $confidence = $match['confidence'];

                    StudentSkill::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'skill_id' => $skill->id,
                        ],
                        [
                            'confidence_score' => $confidence,
                            'source' => 'cv_ai',
                        ]
                    );

                    $savedCount++;
                }
            });

            $history = CVAnalysisHistory::create([
                'student_id' => $student->id,
                'cv_upload_id' => $cvUpload->id,
                'analysis_version' => $analysisVersion,
                'ai_provider' => $this->aiService->getProviderName(),
                'ai_model' => $this->aiService->getModelName(),
                'prompt_version' => self::PROMPT_VERSION,
                'status' => 'completed',
                'summary' => sprintf(
                    'Extracted %d skills (%d catalog matched, %d unmatched) from %s',
                    count($rawSkills),
                    count($matchingResult['matched']),
                    count($matchingResult['unmatched']),
                    $cvUpload->original_filename
                ),
                'raw_result' => [
                    'extracted_raw' => $rawSkills,
                    'matched' => array_map(fn ($m) => [
                        'skill_id' => $m['skill']->id,
                        'name' => $m['skill']->name,
                        'category' => $m['skill']->category,
                        'confidence' => $m['confidence'],
                    ], $matchingResult['matched']),
                    'unmatched' => $matchingResult['unmatched'],
                ],
                'analyzed_at' => now(),
            ]);

            Log::info('AI skill extraction completed successfully', [
                'student_id' => $student->id,
                'saved_skills' => $savedCount,
                'history_id' => $history->id,
            ]);

            return [
                'success' => true,
                'saved_count' => $savedCount,
                'matched_count' => count($matchingResult['matched']),
                'unmatched_count' => count($matchingResult['unmatched']),
                'matched_skills' => array_map(fn ($m) => [
                    'id' => $m['skill']->id,
                    'name' => $m['skill']->name,
                    'category' => $m['skill']->category,
                    'confidence_score' => $m['confidence'],
                    'source' => 'cv_ai',
                ], $matchingResult['matched']),
                'unmatched_skills' => $matchingResult['unmatched'],
                'history' => [
                    'id' => $history->id,
                    'analysis_version' => $history->analysis_version,
                    'summary' => $history->summary,
                    'analyzed_at' => $history->analyzed_at?->format('Y-m-d H:i:s'),
                ],
            ];
        } catch (Throwable $e) {
            Log::error('AI skill extraction failed', [
                'student_id' => $student->id,
                'cv_upload_id' => $cvUpload->id,
                'error' => $e->getMessage(),
            ]);

            CVAnalysisHistory::create([
                'student_id' => $student->id,
                'cv_upload_id' => $cvUpload->id,
                'analysis_version' => $analysisVersion,
                'ai_provider' => $this->aiService->getProviderName(),
                'ai_model' => $this->aiService->getModelName(),
                'prompt_version' => self::PROMPT_VERSION,
                'status' => 'failed',
                'summary' => 'Extraction failed: ' . Str::limit($e->getMessage(), 200),
                'raw_result' => [
                    'error' => $e->getMessage(),
                ],
                'analyzed_at' => now(),
            ]);

            throw new RuntimeException('AI skill extraction could not be completed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Build the structured prompt for skill extraction.
     */
    public function buildPrompt(string $cvText): string
    {
        return <<<PROMPT
You are an expert technical recruiter and skill evaluation assistant.

Analyze the curriculum vitae (CV) text below and extract all technical and professional skills clearly supported by evidence in the text.

Rules:
1. Extract explicit technical skills: programming languages, frameworks, libraries, databases, cloud platforms, developer tools, and data science libraries.
2. Extract verifiable soft skills only when supported by tangible project, leadership, or work context.
3. Assign a confidence score between 0.00 and 1.00 for each skill:
   - 0.90 to 1.00: Explicitly listed under a skills section or demonstrated across multiple projects.
   - 0.70 to 0.89: Mentioned in project descriptions or work experience.
   - 0.50 to 0.69: Indirectly referenced or inferred from coursework.
4. Do NOT hallucinate or invent skills not present in the CV text.
5. Return ONLY a valid JSON object matching the exact schema below.

Required JSON format:
{
  "skills": [
    {
      "name": "PHP",
      "confidence": 0.95
    },
    {
      "name": "Laravel",
      "confidence": 0.92
    }
  ]
}

CV Text to analyze:
---
{$cvText}
---
PROMPT;
    }

    /**
     * Parse and strictly validate the AI JSON response.
     *
     * @param string $aiResponse
     * @return array{skills: list<array{name: string, confidence: float}>}
     * @throws RuntimeException
     */
    public function parseAndValidateResponse(string $aiResponse): array
    {
        // Strip Markdown code block fences if present (```json ... ```)
        $cleanJson = trim($aiResponse);
        if (str_starts_with($cleanJson, '```')) {
            $cleanJson = preg_replace('/^```(?:json)?\s*/i', '', $cleanJson) ?? $cleanJson;
            $cleanJson = preg_replace('/\s*```$/', '', $cleanJson) ?? $cleanJson;
        }

        try {
            $decoded = json_decode($cleanJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('The AI provider returned malformed JSON: ' . $e->getMessage());
        }

        if (! is_array($decoded) || ! isset($decoded['skills']) || ! is_array($decoded['skills'])) {
            throw new RuntimeException('AI response validation failed: Missing or invalid "skills" list in JSON payload.');
        }

        $validatedSkills = [];
        foreach ($decoded['skills'] as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $rawName = trim((string) ($item['name'] ?? ''));
            if ($rawName === '') {
                continue;
            }

            $rawConfidence = $item['confidence'] ?? 0.85;
            $confidence = is_numeric($rawConfidence) ? (float) $rawConfidence : 0.85;

            // Clamp confidence to 0.00 - 1.00
            $confidence = max(0.00, min(1.00, round($confidence, 2)));

            $validatedSkills[] = [
                'name' => $rawName,
                'confidence' => $confidence,
            ];
        }

        return [
            'skills' => $validatedSkills,
        ];
    }

    /**
     * Match a collection of extracted raw skill names against the database catalog.
     *
     * @param list<array{name: string, confidence: float}> $rawSkills
     * @return array{
     *     matched: list<array{skill: Skill, confidence: float, raw_name: string}>,
     *     unmatched: list<array{name: string, confidence: float}>
     * }
     */
    public function matchSkills(array $rawSkills): array
    {
        /** @var Collection<int, Skill> $allSkills */
        $allSkills = Skill::where('is_active', true)->get();

        // Index skills by lowercase name and by slug
        $byLowerName = [];
        $bySlug = [];
        foreach ($allSkills as $skill) {
            $byLowerName[strtolower(trim($skill->name))] = $skill;
            $bySlug[$skill->slug] = $skill;
        }

        $matched = [];
        $unmatched = [];
        $matchedSkillIds = [];

        foreach ($rawSkills as $rawItem) {
            $rawName = $rawItem['name'];
            $confidence = $rawItem['confidence'];

            $skill = $this->resolveSkillModel($rawName, $byLowerName, $bySlug);

            if ($skill) {
                // Prevent duplicate additions in the same extraction run
                if (! isset($matchedSkillIds[$skill->id])) {
                    $matchedSkillIds[$skill->id] = true;
                    $matched[] = [
                        'skill' => $skill,
                        'confidence' => $confidence,
                        'raw_name' => $rawName,
                    ];
                }
            } else {
                $unmatched[] = [
                    'name' => $rawName,
                    'confidence' => $confidence,
                ];
            }
        }

        return [
            'matched' => $matched,
            'unmatched' => $unmatched,
        ];
    }

    /**
     * Resolve a single raw skill name to an existing Skill model.
     *
     * @param string $rawName
     * @param array<string, Skill> $byLowerName
     * @param array<string, Skill> $bySlug
     * @return Skill|null
     */
    protected function resolveSkillModel(string $rawName, array $byLowerName, array $bySlug): ?Skill
    {
        $normalized = trim($rawName);
        $lower = strtolower($normalized);

        // 1. Direct lowercase match
        if (isset($byLowerName[$lower])) {
            return $byLowerName[$lower];
        }

        // 2. Slug match
        $slug = Str::slug($normalized);
        if (isset($bySlug[$slug])) {
            return $bySlug[$slug];
        }

        // 3. Alias / Synonym dictionary match
        if (isset($this->aliasMap[$lower])) {
            $canonicalName = $this->aliasMap[$lower];
            $canonicalLower = strtolower($canonicalName);
            if (isset($byLowerName[$canonicalLower])) {
                return $byLowerName[$canonicalLower];
            }
            $canonicalSlug = Str::slug($canonicalName);
            if (isset($bySlug[$canonicalSlug])) {
                return $bySlug[$canonicalSlug];
            }
        }

        // 4. Strip common noise suffixes like "framework", "library", "programming", "language"
        $stripped = preg_replace('/\b(framework|library|programming language|language|tools|tool|skills|skill)\b/i', '', $normalized);
        $stripped = trim(preg_replace('/\s+/', ' ', (string) $stripped));
        if ($stripped !== '' && $stripped !== $normalized) {
            $strippedLower = strtolower($stripped);
            if (isset($byLowerName[$strippedLower])) {
                return $byLowerName[$strippedLower];
            }
            $strippedSlug = Str::slug($stripped);
            if (isset($bySlug[$strippedSlug])) {
                return $bySlug[$strippedSlug];
            }
            if (isset($this->aliasMap[$strippedLower])) {
                $alias = strtolower($this->aliasMap[$strippedLower]);
                if (isset($byLowerName[$alias])) {
                    return $byLowerName[$alias];
                }
            }
        }

        return null;
    }
}
