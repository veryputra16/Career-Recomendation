<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_name',
        'position',
        'description',
        'location',
        'employment_type',
        'type',
        'source',
        'source_url',
        'application_url',
        'deadline',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    /**
     * Skills associated with this job opportunity.
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skills')
            ->using(JobSkill::class)
            ->withPivot('importance')
            ->withTimestamps();
    }

    /**
     * Direct job_skills pivot rows.
     */
    public function jobSkills(): HasMany
    {
        return $this->hasMany(JobSkill::class);
    }

    /**
     * Student matches with this job opportunity.
     */
    public function jobMatches(): HasMany
    {
        return $this->hasMany(JobMatch::class);
    }
}
