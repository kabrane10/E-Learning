<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'badge_id',
        'progress',
        'earned_at',
        'is_pinned',
    ];

    protected $casts = [
        'progress' => 'array',
        'earned_at' => 'datetime',
        'is_pinned' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    public function isEarned(): bool
    {
        return $this->earned_at !== null;
    }
    
    public function scopeEarned($query)
    {
        return $query->whereNotNull('earned_at');
    }
    
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }
}