<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Students having this skill.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_skills')
            ->using(StudentSkill::class)
            ->withPivot('confidence_score', 'proficiency_level', 'source')
            ->withTimestamps();
    }

    /**
     * Direct student_skills pivot rows.
     */
    public function studentSkills(): HasMany
    {
        return $this->hasMany(StudentSkill::class);
    }

    /**
     * Careers requiring or recommending this skill.
     */
    public function careers(): BelongsToMany
    {
        return $this->belongsToMany(Career::class, 'career_skills')
            ->using(CareerSkill::class)
            ->withPivot('importance', 'minimum_level')
            ->withTimestamps();
    }

    /**
     * Direct career_skills pivot rows.
     */
    public function careerSkills(): HasMany
    {
        return $this->hasMany(CareerSkill::class);
    }

    /**
     * Jobs requiring or preferring this skill.
     */
    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_skills')
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
}
