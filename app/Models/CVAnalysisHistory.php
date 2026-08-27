<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CVAnalysisHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cv_analysis_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'cv_upload_id',
        'analysis_version',
        'ai_provider',
        'ai_model',
        'prompt_version',
        'status',
        'summary',
        'raw_result',
        'analyzed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'raw_result' => 'array',
            'analyzed_at' => 'datetime',
        ];
    }

    /**
     * Student associated with this analysis history.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Uploaded CV file that was analyzed.
     */
    public function cvUpload(): BelongsTo
    {
        return $this->belongsTo(CVUpload::class);
    }

    /**
     * Career recommendations produced from this analysis.
     */
    public function careerRecommendations(): HasMany
    {
        return $this->hasMany(CareerRecommendation::class);
    }

    /**
     * Reports generated from this analysis.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'analysis_history_id');
    }
}
