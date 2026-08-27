<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'student_number',
        'full_name',
        'university',
        'faculty',
        'major',
        'semester',
        'graduation_year',
        'bio',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'semester' => 'integer',
            'graduation_year' => 'integer',
        ];
    }

    /**
     * User associated with the student profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * CV uploads by this student.
     */
    public function cvUploads(): HasMany
    {
        return $this->hasMany(CVUpload::class);
    }

    /**
     * CV analysis histories for this student.
     */
    public function cvAnalysisHistories(): HasMany
    {
        return $this->hasMany(CVAnalysisHistory::class);
    }

    /**
     * Skills associated with the student through the student_skills pivot table.
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'student_skills')
            ->using(StudentSkill::class)
            ->withPivot('confidence_score', 'proficiency_level', 'source')
            ->withTimestamps();
    }

    /**
     * Direct relationship to student_skills records.
     */
    public function studentSkills(): HasMany
    {
        return $this->hasMany(StudentSkill::class);
    }

    /**
     * Career recommendations for this student.
     */
    public function careerRecommendations(): HasMany
    {
        return $this->hasMany(CareerRecommendation::class);
    }

    /**
     * Job matches for this student.
     */
    public function jobMatches(): HasMany
    {
        return $this->hasMany(JobMatch::class);
    }

    /**
     * Generated reports for this student.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
