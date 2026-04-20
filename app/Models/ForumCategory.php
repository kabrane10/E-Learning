<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class ForumCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (!$category->slug) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function topics(): HasMany
    {
        return $this->hasMany(ForumTopic::class, 'category_id');
    }

    public function subscriptions(): MorphMany
    {
        return $this->morphMany(ForumSubscription::class, 'subscribable');
    }

    public function getTopicsCountAttribute(): int
    {
        return $this->topics()->count();
    }

    public function getPostsCountAttribute(): int
    {
        return $this->topics()->sum('posts_count');
    }

    public function getLastTopicAttribute()
    {
        return $this->topics()
            ->whereNotNull('last_post_at')
            ->orderBy('last_post_at', 'desc')
            ->first();
    }

    public function isSubscribedBy(User $user): bool
    {
        return $this->subscriptions()->where('user_id', $user->id)->exists();
    }
}