<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'course_id',
        'title',
        'created_by',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'participants')
            ->withPivot('role', 'last_read_at', 'is_muted', 'is_pinned')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function lastMessage(): HasMany
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function unreadMessagesForUser(int $userId): int
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        
        if (!$participant || !$participant->last_read_at) {
            return $this->messages()->count();
        }

        return $this->messages()
            ->where('created_at', '>', $participant->last_read_at)
            ->where('user_id', '!=', $userId)
            ->count();
    }

    public function markAsRead(int $userId): void
    {
        $this->participants()
            ->where('user_id', $userId)
            ->update(['last_read_at' => now()]);
    }

    public function isParticipant(int $userId): bool
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    public function addParticipant(int $userId, string $role = 'member'): void
    {
        $this->participants()->firstOrCreate(
            ['user_id' => $userId],
            ['role' => $role, 'joined_at' => now()]
        );
    }

    public function removeParticipant(int $userId): void
    {
        $this->participants()->where('user_id', $userId)->delete();
    }
}