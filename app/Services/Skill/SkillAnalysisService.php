<?php

namespace App\Services\Skill;

use App\Models\Skill;
use App\Models\Student;
use Illuminate\Support\Collection;

class SkillAnalysisService
{
    /**
     * Human-friendly labels for skill categories.
     *
     * @var array<string, string>
     */
    protected array $categoryLabels = [
        'language' => 'Programming Languages',
        'framework' => 'Frameworks & Libraries',
        'database' => 'Databases',
        'tool' => 'Developer Tools',
        'cloud' => 'Cloud & DevOps',
        'technical' => 'Technical Fundamentals',
        'soft_skill' => 'Soft Skills',
        'other' => 'Other Competencies',
    ];

    /**
     * Generate complete skill profile analysis for a student.
     *
     * @param Student|null $student
     * @return array<string, mixed>
     */
    public function analyze(?Student $student): array
    {
        if (! $student) {
            return $this->emptyAnalysis();
        }

        /** @var Collection<int, Skill> $skills */
        $skills = $student->skills()
            ->withPivot('confidence_score', 'proficiency_level', 'source', 'created_at')
            ->orderBy('skills.name')
            ->get();

        $totalSkills = $skills->count();

        if ($totalSkills === 0) {
            return $this->emptyAnalysis();
        }

        // Transform individual skills
        $formattedSkills = $skills->map(function (Skill $skill) {
            $confidence = $skill->pivot->confidence_score !== null
                ? (float) $skill->pivot->confidence_score
                : null;

            $confidencePercentage = $confidence !== null
                ? (int) round($confidence <= 1.0 ? $confidence * 100 : $confidence)
                : null;

            $categoryKey = strtolower(trim((string) $skill->category)) ?: 'other';
            $categoryLabel = $this->resolveCategoryLabel($categoryKey);

            $sourceKey = (string) ($skill->pivot->source ?? 'manual');
            $sourceLabel = match ($sourceKey) {
                'cv_ai' => 'CV Extraction (AI)',
                'manual' => 'Manual Entry',
                'admin' => 'Admin Verified',
                default => ucfirst($sourceKey),
            };

            return [
                'id' => $skill->id,
                'name' => $skill->name,
                'slug' => $skill->slug,
                'category' => $categoryKey,
                'category_label' => $categoryLabel,
                'description' => $skill->description,
                'confidence_score' => $confidence,
                'confidence_percentage' => $confidencePercentage,
                'proficiency_level' => $skill->pivot->proficiency_level,
                'source' => $sourceKey,
                'source_label' => $sourceLabel,
                'identified_at' => $skill->pivot->created_at?->format('Y-m-d H:i'),
            ];
        })->values();

        // Group by category
        $grouped = $formattedSkills->groupBy('category');

        $categories = $grouped->map(function (Collection $categorySkills, string $categoryKey) use ($totalSkills) {
            $count = $categorySkills->count();
            $percentage = $totalSkills > 0 ? round(($count / $totalSkills) * 100, 1) : 0.0;
            $label = $this->resolveCategoryLabel($categoryKey);

            // Calculate category average confidence if available
            $confidences = $categorySkills->pluck('confidence_score')->filter(fn ($c) => $c !== null);
            $avgConfidence = $confidences->isNotEmpty() ? round($confidences->avg(), 2) : null;
            $avgConfidencePercentage = $avgConfidence !== null
                ? (int) round($avgConfidence <= 1.0 ? $avgConfidence * 100 : $avgConfidence)
                : null;

            return [
                'key' => $categoryKey,
                'label' => $label,
                'count' => $count,
                'percentage' => $percentage,
                'average_confidence' => $avgConfidence,
                'average_confidence_percentage' => $avgConfidencePercentage,
                'skills' => $categorySkills->values()->all(),
            ];
        })->sortByDesc('count')->values();

        // Source distribution
        $sourceBreakdown = [
            'cv_ai' => $formattedSkills->where('source', 'cv_ai')->count(),
            'manual' => $formattedSkills->where('source', 'manual')->count(),
            'other' => $formattedSkills->whereNotIn('source', ['cv_ai', 'manual'])->count(),
        ];

        // Overall average confidence
        $allConfidences = $formattedSkills->pluck('confidence_score')->filter(fn ($c) => $c !== null);
        $averageConfidence = $allConfidences->isNotEmpty() ? round($allConfidences->avg(), 2) : null;
        $averageConfidencePercentage = $averageConfidence !== null
            ? (int) round($averageConfidence <= 1.0 ? $averageConfidence * 100 : $averageConfidence)
            : null;

        $topCategory = $categories->first();

        return [
            'total_skills' => $totalSkills,
            'total_categories' => $categories->count(),
            'top_category' => $topCategory ? [
                'key' => $topCategory['key'],
                'label' => $topCategory['label'],
                'count' => $topCategory['count'],
                'percentage' => $topCategory['percentage'],
            ] : null,
            'average_confidence' => $averageConfidence,
            'average_confidence_percentage' => $averageConfidencePercentage,
            'source_breakdown' => $sourceBreakdown,
            'categories' => $categories->all(),
            'skills' => $formattedSkills->all(),
        ];
    }

    /**
     * Resolve a human-friendly label for a skill category.
     *
     * @param string $category
     * @return string
     */
    public function resolveCategoryLabel(string $category): string
    {
        $key = strtolower(trim($category));

        if (isset($this->categoryLabels[$key])) {
            return $this->categoryLabels[$key];
        }

        if ($key === '') {
            return 'Uncategorized';
        }

        return ucwords(str_replace(['_', '-'], ' ', $key));
    }

    /**
     * Return the default empty analysis structure.
     *
     * @return array<string, mixed>
     */
    protected function emptyAnalysis(): array
    {
        return [
            'total_skills' => 0,
            'total_categories' => 0,
            'top_category' => null,
            'average_confidence' => null,
            'average_confidence_percentage' => null,
            'source_breakdown' => [
                'cv_ai' => 0,
                'manual' => 0,
                'other' => 0,
            ],
            'categories' => [],
            'skills' => [],
        ];
    }
}
