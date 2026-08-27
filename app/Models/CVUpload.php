<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CVUpload extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cv_uploads';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'original_filename',
        'stored_filename',
        'file_path',
        'file_size',
        'mime_type',
        'status',
        'extracted_text',
        'uploaded_at',
        'processed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'uploaded_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Student who uploaded this CV.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Historical analysis records of this CV upload.
     */
    public function cvAnalysisHistories(): HasMany
    {
        return $this->hasMany(CVAnalysisHistory::class);
    }
}
