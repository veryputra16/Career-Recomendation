<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Career extends Model
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
        'description',
        'category',
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
     * Skills required or recommended for this career.
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'career_skills')
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
     * Recommendations made for this career.
     */
    public function careerRecommendations(): HasMany
    {
        return $this->hasMany(CareerRecommendation::class);
    }
}
