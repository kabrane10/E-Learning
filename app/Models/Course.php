<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'instructor_id',
        'title',
        'slug',
        'short_description',
        'description',
        'level',
        'category',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // ==================== RELATIONS ====================
    
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
                    ->withPivot('progress_percentage', 'completed_at')
                    ->withTimestamps();
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ==================== MEDIA LIBRARY ====================
    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
             ->singleFile()
             ->useFallbackUrl('/images/default-course.jpg')
             ->registerMediaConversions(function (Media $media) {
                 $this->addMediaConversion('thumb')
                      ->width(368)
                      ->height(232)
                      ->sharpen(10);
                 $this->addMediaConversion('card')
                      ->width(736)
                      ->height(464);
             });
    }

    // ==================== ACCESSORS ====================
    
    public function getThumbnailUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('thumbnail', 'card') ?: '/images/default-course.jpg';
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getTotalDurationAttribute(): int
    {
        return $this->lessons()->sum('duration');
    }

    public function getFormattedDurationAttribute(): string
    {
        $totalSeconds = $this->total_duration;
        
        if ($totalSeconds === 0) {
            return '0min';
        }
        
        $totalMinutes = floor($totalSeconds / 60);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . ($minutes > 0 ? $minutes . 'min' : '');
        }
        
        return $minutes . 'min';
    }

    // ==================== MÉTHODES UTILITAIRES ====================
    
    public function isBookmarkedByUser($userId): bool
    {
        return $this->bookmarks()->where('user_id', $userId)->exists();
    }

    public function getUserProgress($userId): int
    {
        $enrollment = $this->enrollments()
            ->where('user_id', $userId)
            ->first();
            
        return $enrollment ? $enrollment->progress_percentage : 0;
    }

    public function getUserReview($userId): ?Review
    {
        return $this->reviews()->where('user_id', $userId)->first();
    }

    public function isUserEnrolled($userId): bool
    {
        return $this->enrollments()->where('user_id', $userId)->exists();
    }

    public function getEnrollmentForUser($userId): ?Enrollment
    {
        return $this->enrollments()->where('user_id', $userId)->first();
    }

    // ==================== BOOT ====================
    
    protected static function booted(): void
    {
        // Génération automatique du slug
        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = \Illuminate\Support\Str::slug($course->title);
            }
        });
        
        static::updating(function ($course) {
            if ($course->isDirty('title') && !$course->isDirty('slug')) {
                $course->slug = \Illuminate\Support\Str::slug($course->title);
            }
        });

        // Supprimer les médias lors de la suppression du cours
        static::deleting(function ($course) {
            $course->clearMediaCollection('thumbnail');
        });
    }
}