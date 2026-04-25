<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Str;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'instructor_id',
        'title',
        'slug',
        'category',     
        'level',
        'is_free',
        'price',
        'short_description',
        'description',
        'learning_outcomes',
        'prerequisites',
        'target_audience',
        'is_published',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_published' => 'boolean',
        'price' => 'float',
        'learning_outcomes' => 'array',
        'prerequisites' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::creating(function ($course) {
            if (!$course->slug) {
                $course->slug = Str::slug($course->title) . '-' . Str::random(6);
            }
        });
    }

    /**
     * Get resources.
     */
    public function getResourcesAttribute()
    {
        return $this->getMedia('resources');
    }

    /**
     * Get the instructor that owns the course.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the students enrolled in the course.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot('progress_percentage', 'completed_at', 'created_at as enrolled_at')
            ->withTimestamps();
    }

    /**
     * Get the enrollments for the course.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the lessons for the course.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    /**
     * Get the chapters for the course.
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    /**
     * Get the reviews for the course.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
        $this->addMediaCollection('promo_video')->singleFile();
        $this->addMediaCollection('resources');
    }

    /**
     * Register media conversions.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10);
            
        $this->addMediaConversion('card')
            ->width(736)
            ->height(464);
    }

    /**
 * Get the thumbnail URL.
 */
   public function getThumbnailUrlAttribute(): string
   {
       $media = $this->getFirstMedia('thumbnail');
    
       if ($media) {
           // ✅ URL absolue
           return asset('storage/' . $media->id . '/' . $media->file_name);
       }
    
       // Fallback
       return 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?w=400';
   }

    /**
     * Get the promo video URL.
     */
    public function getPromoVideoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('promo_video') ?: null;
    }

    /**
     * Get the students count attribute.
     */
    public function getStudentsCountAttribute(): int
    {
        return $this->students()->count();
    }

    /**
     * Get the lessons count attribute.
     */
    public function getLessonsCountAttribute(): int
    {
        return $this->lessons()->count();
    }

    /**
     * Get the reviews count attribute.
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Get the average rating attribute.
     */
    public function getAverageRatingAttribute(): ?float
    {
        return $this->reviews()->avg('rating');
    }

    /**
     * Get the completion rate attribute.
     */
    public function getCompletionRateAttribute(): float
    {
        $total = $this->students_count;
        if ($total === 0) {
            return 0;
        }

        $completed = $this->students()
            ->whereNotNull('enrollments.completed_at')
            ->count();

        return round(($completed / $total) * 100);
    }

    /**
     * Check if user is enrolled in the course.
     */
    public function isUserEnrolled(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->students()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the user's progress in the course.
     */
    public function getUserProgress(?User $user): int
    {
        if (!$user) {
            return 0;
        }

        $enrollment = $this->enrollments()->where('user_id', $user->id)->first();
        return $enrollment ? $enrollment->progress_percentage : 0;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->is_free) {
            return 'Gratuit';
        }
        return number_format($this->price, 2) . ' FCFA';
    }

    /**
     * Get level label.
     */
    public function getLevelLabelAttribute(): string
    {
        return match($this->level) {
            'beginner' => 'Débutant',
            'intermediate' => 'Intermédiaire',
            'advanced' => 'Avancé',
            default => $this->level,
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_published ? 'Publié' : 'Brouillon';
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute(): string
    {
        return $this->is_published ? 'green' : 'yellow';
    }

    /**
 * Get all quizzes for this course (via lessons).
 */
public function quizzes()
{
    return $this->hasManyThrough(Quiz::class, Lesson::class);
}
  
      /**
     * Check if a user has access to this course.
     */
    public function isAccessibleBy(?User $user): bool
    {
        // Cours publié
        if (!$this->is_published) {
            return false;
        }
        
        // Cours gratuit : accessible à tous
        if ($this->is_free) {
            return true;
        }
        
        // Cours payant : vérifier l'inscription
        if (!$user) {
            return false;
        }
        
        // L'instructeur a toujours accès
        if ($user->id === $this->instructor_id) {
            return true;
        }
        
        // Admin a toujours accès
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Vérifier si l'étudiant est inscrit (a payé)
        return $this->students()
            ->where('user_id', $user->id)
            ->exists();
    }


}