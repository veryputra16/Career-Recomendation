<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobMatch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'job_id',
        'matching_score',
        'matched_skills',
        'missing_skills',
        'explanation',
        'matching_method',
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
            'matched_skills' => 'array',
            'missing_skills' => 'array',
        ];
    }

    /**
     * Student associated with this job match.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Matched job opportunity.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
