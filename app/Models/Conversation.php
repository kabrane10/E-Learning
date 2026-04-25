<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    /**
     * Get the creator of the conversation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the course associated with the conversation.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get all participants of the conversation.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Get all users in the conversation (through participants).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'participants')
            ->withPivot('role', 'last_read_at', 'is_muted', 'is_pinned')
            ->withTimestamps();
    }

    /**
     * Get all messages in the conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the last message of the conversation.
     * CORRECTION : Type de retour HasOne
     */
    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Count unread messages for a specific user.
     */
    public function unreadMessagesForUser(int $userId): int
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        
        if (!$participant) {
            return 0;
        }
        
        if (!$participant->last_read_at) {
            return $this->messages()
                ->where('user_id', '!=', $userId)
                ->count();
        }

        return $this->messages()
            ->where('created_at', '>', $participant->last_read_at)
            ->where('user_id', '!=', $userId)
            ->count();
    }

    /**
     * Mark conversation as read for a user.
     */
    public function markAsRead(int $userId): void
    {
        $this->participants()
            ->where('user_id', $userId)
            ->update(['last_read_at' => now()]);
    }

    /**
     * Check if a user is a participant.
     */
    public function isParticipant(int $userId): bool
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    /**
     * Add a participant to the conversation.
     */
    public function addParticipant(int $userId, string $role = 'member'): void
    {
        $exists = $this->participants()->where('user_id', $userId)->exists();
        
        if (!$exists) {
            $this->participants()->create([
                'user_id' => $userId,
                'role' => $role,
                'joined_at' => now(),
            ]);
        }
    }

    /**
     * Remove a participant from the conversation.
     */
    public function removeParticipant(int $userId): void
    {
        $this->participants()->where('user_id', $userId)->delete();
    }

    /**
     * Get the other user in a private conversation.
     */
    public function getOtherUserAttribute(): ?User
    {
        if ($this->type !== 'private') {
            return null;
        }
        
        return $this->users()
            ->where('user_id', '!=', auth()->id())
            ->first();
    }

    /**
     * Get the title of the conversation.
     */
    public function getTitleAttribute($value): string
    {
        if ($value) {
            return $value;
        }
        
        if ($this->type === 'private' && $this->other_user) {
            return $this->other_user->name;
        }
        
        if ($this->type === 'course' && $this->course) {
            return $this->course->title;
        }
        
        return 'Conversation';
    }
}