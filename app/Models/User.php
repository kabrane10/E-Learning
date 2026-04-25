<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;         
use Spatie\MediaLibrary\InteractsWithMedia; 

class User extends Authenticatable implements HasMedia

// class User extends Authenticatable 
//  implements MustVerifyEmail // Important pour la vérification d'email
{
    use HasFactory, Notifiable, HasRoles;
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'title',
        'bio',
        'website',
        'twitter',
        'linkedin',
        'youtube',
        'settings',
        'provider',
        'provider_id',
        'avatar',
        'total_points',
        'current_level',
        'experience_points',
        'streak_days',
        'last_activity_at',
        'last_login_at',
        'preferences',
        'admin_notes',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'settings' => 'array',
        'total_points' => 'integer',
        'current_level' => 'integer',
        'experience_points' => 'integer',
        'streak_days' => 'integer',
        'last_activity_at' => 'datetime',
        'last_login_at' => 'datetime',
        'preferences' => 'array',
    ];
    /**
     * Get the courses taught by the user (instructor).
     */
    public function taughtCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Get the courses the user is enrolled in.
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
                    ->withPivot('progress_percentage', 'completed_at')
                    ->withTimestamps();
    }

    /**
     * Get the user's enrollments.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the user's lesson completions.
     */
    public function lessonCompletions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    /**
     * Get the user's quiz attempts.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get the user's bookmarked courses.
     */
    public function bookmarkedCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'bookmarks')->withTimestamps();
    }

    /**
     * Get the user's bookmarks.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Get the user's reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the user's activity logs.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get the user's notifications.
     */
    public function notifications(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable');
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=4F46E5&color=fff&size=128';
    }

    /**
     * Get total learning hours.
     */
    public function getTotalLearningHoursAttribute(): float
    {
        return $this->lessonCompletions()
            ->join('lessons', 'lesson_completions.lesson_id', '=', 'lessons.id')
            ->sum('lessons.duration') / 3600;
    }

    /**
     * Get completed courses count.
     */
    public function getCompletedCoursesCountAttribute(): int
    {
        return $this->enrollments()
            ->whereNotNull('completed_at')
            ->count();
    }

    /**
     * Get average quiz score.
     */
    public function getAverageQuizScoreAttribute(): float
    {
        return $this->quizAttempts()
            ->whereNotNull('completed_at')
            ->avg('score') ?? 0;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is instructor.
     */
    public function isInstructor(): bool
    {
        return $this->hasRole('instructor');
    }

    /**
     * Check if user is student.
     */
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * Get user's progress in a specific course.
     */
    public function getProgressInCourse(Course $course): int
    {
        $enrollment = $this->enrollments()
            ->where('course_id', $course->id)
            ->first();
            
        return $enrollment ? $enrollment->progress_percentage : 0;
    }

    /**
     * Check if user has completed a specific course.
     */
    public function hasCompletedCourse(Course $course): bool
    {
        return $this->enrollments()
            ->where('course_id', $course->id)
            ->whereNotNull('completed_at')
            ->exists();
    }

    /**
     * Check if user is enrolled in a specific course.
     */
    public function isEnrolledIn(Course $course): bool
    {
        return $this->enrolledCourses()
            ->where('course_id', $course->id)
            ->exists();
    }

    /**
     * Get user's certificate for a completed course.
     */
    public function getCertificate(Course $course)
    {
        return $this->enrollments()
            ->where('course_id', $course->id)
            ->whereNotNull('completed_at')
            ->first();
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new CustomVerifyEmail());
    }

    /**
     * Find or create a user from social provider data.
     */
    public static function findOrCreateFromSocial($provider, $providerUser)
    {
        // Chercher l'utilisateur par provider_id
        $user = self::where('provider', $provider)
            ->where('provider_id', $providerUser->getId())
            ->first();

        if ($user) {
            // Mettre à jour l'avatar si changé
            if ($providerUser->getAvatar() && $user->avatar !== $providerUser->getAvatar()) {
                $user->update(['avatar' => $providerUser->getAvatar()]);
            }
            return $user;
        }

        // Chercher par email
        $user = self::where('email', $providerUser->getEmail())->first();

        if ($user) {
            // Lier le compte social au compte existant
            $user->update([
                'provider' => $provider,
                'provider_id' => $providerUser->getId(),
                'avatar' => $providerUser->getAvatar() ?: $user->avatar,
            ]);
            return $user;
        }

        // Créer un nouvel utilisateur
        $name = $providerUser->getName() ?: explode('@', $providerUser->getEmail())[0];
        
        $user = self::create([
            'name' => $name,
            'email' => $providerUser->getEmail(),
            'provider' => $provider,
            'provider_id' => $providerUser->getId(),
            'avatar' => $providerUser->getAvatar(),
            'email_verified_at' => now(), // Email vérifié par le provider
            'password' => bcrypt(\Illuminate\Support\Str::random(32)),
        ]);

        // Assigner le rôle par défaut (student)
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('student');
        }

        return $user;
    }

    public function conversations(): BelongsToMany
{
    return $this->belongsToMany(Conversation::class, 'participants')
        ->withPivot('role', 'last_read_at', 'is_muted', 'is_pinned')
        ->withTimestamps();
}

public function participants(): HasMany
{
    return $this->hasMany(Participant::class);
}

public function messages(): HasMany
{
    return $this->hasMany(Message::class);
}

public function unreadMessagesCount(): int
{
    $total = 0;
    
    foreach ($this->conversations as $conversation) {
        $total += $conversation->unreadMessagesForUser($this->id);
    }
    
    return $total;
}

 // =========================================================================
    // Relations pour la gamification
    // =========================================================================

    /**
     * Get the user's badges.
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot('progress', 'earned_at', 'is_pinned')
                    ->withTimestamps();
    }

    /**
     * Get the user's badges pivot.
     */
    public function userBadges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    /**
     * Get the user's points.
     */
    public function points(): HasMany
    {
        return $this->hasMany(Point::class);
    }

    /**
     * Get the user's achievements.
     */
    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
                    ->withPivot('progress', 'current_tier', 'completed_at', 'claimed_at')
                    ->withTimestamps();
    }

    /**
     * Get the user's achievements pivot.
     */
    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }
/**
 * Get the level associated with the user.
 */
    public function levelRelation(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'current_level', 'level_number');
    }


}
