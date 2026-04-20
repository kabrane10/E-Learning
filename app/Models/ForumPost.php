<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'topic_id',
        'user_id',
        'content',
        'parent_id',
        'likes_count',
        'is_solution',
        'is_edited',
        'edited_at',
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'is_solution' => 'boolean',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function ($post) {
            // Mettre à jour le compteur du topic
            $post->topic->increment('posts_count');
            $post->topic->updateLastPost($post);

            // Points pour la création d'un post
            app(\App\Services\GamificationService::class)->addPoints(
                $post->user,
                'forum_post_created',
                $post
            );

            // Notifier les abonnés
            $post->topic->notifySubscribers($post);
        });

        static::updated(function ($post) {
            if ($post->isDirty('content')) {
                $post->update(['is_edited' => true, 'edited_at' => now()]);
            }
        });
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumPost::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(ForumLike::class, 'likeable');
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function toggleLike(User $user): void
    {
        $like = $this->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $this->decrement('likes_count');
            $this->topic->decrement('likes_count');
        } else {
            $this->likes()->create(['user_id' => $user->id]);
            $this->increment('likes_count');
            $this->topic->increment('likes_count');
            
            // Points pour le like
            if ($this->user_id !== $user->id) {
                app(\App\Services\GamificationService::class)->addPoints(
                    $this->user,
                    'post_liked',
                    $this
                );
            }
        }
    }

    public function notifySubscribers(): void
    {
        $subscribers = $this->topic->subscriptions()
            ->where('user_id', '!=', $this->user_id)
            ->get();

        foreach ($subscribers as $subscription) {
            // Envoyer une notification selon le type d'abonnement
            if ($subscription->type === 'instant') {
                $subscription->user->notify(new \App\Notifications\NewForumReply($this));
            }
        }
    }
}