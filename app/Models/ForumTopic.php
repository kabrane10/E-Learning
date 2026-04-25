<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ForumTopic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_id',
        'course_id',
        'user_id',
        'type',
        'status',
        'views_count',
        'posts_count',
        'likes_count',
        'is_sticky',
        'is_announcement',
        'last_post_at',
        'last_post_user_id',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'posts_count' => 'integer',
        'likes_count' => 'integer',
        'is_sticky' => 'boolean',
        'is_announcement' => 'boolean',
        'last_post_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($topic) {
            if (!$topic->slug) {
                $topic->slug = Str::slug($topic->title) . '-' . Str::random(6);
            }
        });

        static::created(function ($topic) {
            // Points pour la création d'un sujet (si le service existe)
            if (class_exists(\App\Services\GamificationService::class)) {
                app(\App\Services\GamificationService::class)->addPoints(
                    $topic->user,
                    'forum_topic_created',
                    $topic
                );
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lastPostUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_post_user_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(ForumPost::class, 'topic_id')
            ->whereNull('parent_id')
            ->orderBy('created_at', 'asc');
    }

    public function allPosts(): HasMany
    {
        return $this->hasMany(ForumPost::class, 'topic_id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(ForumLike::class, 'likeable');
    }

    public function subscriptions(): MorphMany
    {
        return $this->morphMany(ForumSubscription::class, 'subscribable');
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function updateLastPost(ForumPost $post): void
    {
        $this->update([
            'last_post_at' => $post->created_at,
            'last_post_user_id' => $post->user_id,
        ]);
    }

    public function markAsResolved(ForumPost $solution = null): void
    {
        $this->update(['status' => 'resolved']);
        
        if ($solution) {
            $solution->update(['is_solution' => true]);
        }
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function isSubscribedBy(User $user): bool
    {
        return $this->subscriptions()->where('user_id', $user->id)->exists();
    }

    public function getUrlAttribute(): string
    {
        return route('forum.topics.show', $this);
    }

    /**
     * Get the excerpt of the content.
     * Cet accesseur ne prend pas de paramètre.
     */
    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->content), 200);
    }

    /**
     * Notify all subscribers of a new reply.
     * Cette méthode est appelée depuis ForumPost lorsqu'un nouveau message est créé.
     */
    public function notifySubscribers(ForumPost $post): void
    {
        // Récupérer tous les abonnés sauf l'auteur du post
        $subscribers = $this->subscriptions()
            ->with('user')
            ->where('user_id', '!=', $post->user_id)
            ->get();

        foreach ($subscribers as $subscription) {
            // Vérifier le type d'abonnement
            if ($subscription->type === 'instant') {
                // Notification immédiate
                $this->sendNotification($subscription->user, $post);
            } elseif ($subscription->type === 'daily') {
                // Stocker pour envoi quotidien (à implémenter)
                $this->queueDailyNotification($subscription->user, $post);
            } elseif ($subscription->type === 'weekly') {
                // Stocker pour envoi hebdomadaire (à implémenter)
                $this->queueWeeklyNotification($subscription->user, $post);
            }
        }
    }

    /**
     * Send immediate notification to a user.
     */
    private function sendNotification(User $user, ForumPost $post): void
    {
        try {
            // Notification dans l'application
            $user->notify(new \App\Notifications\NewForumReply($post));
            
            // Mise à jour de la date de dernière notification
            $this->subscriptions()
                ->where('user_id', $user->id)
                ->update(['last_notified_at' => now()]);
                
        } catch (\Exception $e) {
            \Log::error('Erreur envoi notification forum: ' . $e->getMessage());
        }
    }

    /**
     * Queue daily notification.
     */
    private function queueDailyNotification(User $user, ForumPost $post): void
    {
        // À implémenter : stocker dans une table de notifications différées
        // Pour l'instant, on envoie quand même
        $this->sendNotification($user, $post);
    }

    /**
     * Queue weekly notification.
     */
    private function queueWeeklyNotification(User $user, ForumPost $post): void
    {
        // À implémenter : stocker dans une table de notifications différées
        // Pour l'instant, on envoie quand même
        $this->sendNotification($user, $post);
    }

    /**
     * Get an excerpt with custom length.
     */
    public function excerpt(int $length = 200): string
    {
        return Str::limit(strip_tags($this->content), $length);
    }

}