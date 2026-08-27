<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerRecommendation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'career_id',
        'cv_analysis_history_id',
        'matching_score',
        'reason',
        'existing_skills',
        'missing_skills',
        'suggestions',
        'recommendation_source',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'matching_score' => 'float',
            'existing_skills' => 'array',
            'missing_skills' => 'array',
            'suggestions' => 'array',
        ];
    }

    /**
     * Student who received this recommendation.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Career recommended to the student.
     */
    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    /**
     * Analysis history record corresponding to this recommendation.
     */
    public function cvAnalysisHistory(): BelongsTo
    {
        return $this->belongsTo(CVAnalysisHistory::class, 'cv_analysis_history_id');
    }
}
