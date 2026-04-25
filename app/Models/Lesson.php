<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Lesson extends Model implements HasMedia
{
    use HasFactory,  InteractsWithMedia;

    protected $fillable = [
        'course_id',
        'chapter_id',
        'title',
        'content_type',
        'content',
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

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('video')->singleFile();
        $this->addMediaCollection('pdf')->singleFile();
        $this->addMediaCollection('attachments');
    }

    /**
     * Get the video URL.
     */
    public function getVideoUrlAttribute(): ?string
    {
        // Vérifier d'abord le nouveau système Media Library
        $mediaUrl = $this->getFirstMediaUrl('video');
        if ($mediaUrl) return $mediaUrl;
        
        // Fallback sur l'ancien champ
        if ($this->video_path) return asset('storage/' . $this->video_path);
        
        return null;
    }

    /**
     * Get the PDF URL.
     */
    public function getPdfUrlAttribute(): ?string
    {
        $mediaUrl = $this->getFirstMediaUrl('pdf');
        if ($mediaUrl) return $mediaUrl;
        
        if ($this->pdf_path) return asset('storage/' . $this->pdf_path);
        
        return null;
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

     // ✅ Accesseur pour compatibilité avec l'ancien nom 'type'
     public function getTypeAttribute(): string
     {
         return $this->content_type ?? 'video';
     }
 
     // ✅ Mutateur pour compatibilité
     public function setTypeAttribute($value): void
     {
         $this->attributes['content_type'] = $value;
     }
 
}
