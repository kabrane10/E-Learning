<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'chapter_id',
        'title',
        'content_type',
        'video_path',
        'pdf_path',
        'duration',
        'order',
        'is_free_preview',
    ];

    protected $casts = [
        'duration' => 'integer',
        'order' => 'integer',
        'is_free_preview' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    public function isCompletedByUser($userId): bool
    {
        return $this->completions()
            ->where('user_id', $userId)
            ->exists();
    }

    public function hasQuiz(): bool
    {
        return $this->quiz()->exists();
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video_path ? asset('storage/' . $this->video_path) : null;
    }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path ? asset('storage/' . $this->pdf_path) : null;
    }

    public function getIconAttribute(): string
    {
        return match($this->content_type) {
            'video' => 'fa-play-circle',
            'pdf' => 'fa-file-pdf',
            'quiz' => 'fa-puzzle-piece',
            'text' => 'fa-file-alt',
            default => 'fa-file'
        };
    }
}