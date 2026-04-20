<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAchievement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'achievement_id',
        'progress',
        'current_tier',
        'completed_at',
        'claimed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'progress' => 'array',
        'current_tier' => 'integer',
        'completed_at' => 'datetime',
        'claimed_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (UserAchievement $userAchievement) {
            if (!$userAchievement->progress) {
                $userAchievement->progress = [
                    'current' => 0,
                    'target' => 0,
                    'percentage' => 0,
                ];
            }
        });
    }

    /**
     * Get the user that owns the achievement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the achievement.
     */
    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    /**
     * Check if the achievement is completed.
     */
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    /**
     * Check if the reward has been claimed.
     */
    public function isClaimed(): bool
    {
        return $this->claimed_at !== null;
    }

     /**
     * Scope for completed achievements.
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    /**
     * Scope for unclaimed achievements.
     */
    public function scopeUnclaimed($query)
    {
        return $query->whereNotNull('completed_at')->whereNull('claimed_at');
    }

    /**
     * Check if the reward can be claimed.
     */
    public function canClaim(): bool
    {
        return $this->isCompleted() && !$this->isClaimed();
    }

    /**
     * Mark the achievement as completed.
     */
    public function markAsCompleted(): void
    {
        if (!$this->completed_at) {
            $this->update([
                'completed_at' => now(),
                'progress' => array_merge($this->progress ?? [], [
                    'percentage' => 100,
                    'completed_at' => now()->toISOString(),
                ]),
            ]);
        }
    }

    /**
     * Mark the reward as claimed.
     */
    public function markAsClaimed(): void
    {
        if ($this->canClaim()) {
            $this->update(['claimed_at' => now()]);
        }
    }

    /**
     * Update the progress of the achievement.
     */
    public function updateProgress(int $current, int $target): void
    {
        $percentage = $target > 0 ? min(100, round(($current / $target) * 100)) : 0;
        
        $this->update([
            'progress' => [
                'current' => $current,
                'target' => $target,
                'percentage' => $percentage,
                'updated_at' => now()->toISOString(),
            ],
        ]);

        // Vérifier si l'achievement est complété
        if ($percentage >= 100 && !$this->completed_at) {
            $this->markAsCompleted();
        }
    }

    /**
     * Get the progress percentage.
     */
    public function getProgressPercentageAttribute(): int
    {
        return $this->progress['percentage'] ?? 0;
    }

    /**
     * Get the current progress value.
     */
    public function getProgressCurrentAttribute(): int
    {
        return $this->progress['current'] ?? 0;
    }

    /**
     * Get the target value.
     */
    public function getProgressTargetAttribute(): int
    {
        return $this->progress['target'] ?? 0;
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->isClaimed()) {
            return 'Réclamé';
        }
        
        if ($this->isCompleted()) {
            return 'À réclamer';
        }
        
        return 'En cours';
    }

    /**
     * Get the status color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status_label) {
            'Réclamé' => 'green',
            'À réclamer' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Scope a query to only include in-progress achievements.
     */
    public function scopeInProgress($query)
    {
        return $query->whereNull('completed_at');
    }

    /**
     * Get the tier name based on current tier.
     */
    public function getTierNameAttribute(): string
    {
        return match ($this->current_tier) {
            1 => 'Bronze',
            2 => 'Argent',
            3 => 'Or',
            4 => 'Platine',
            5 => 'Diamant',
            default => 'Standard',
        };
    }

    /**
     * Get the tier color based on current tier.
     */
    public function getTierColorAttribute(): string
    {
        return match ($this->current_tier) {
            1 => '#CD7F32', // Bronze
            2 => '#C0C0C0', // Argent
            3 => '#FFD700', // Or
            4 => '#E5E4E2', // Platine
            5 => '#B9F2FF', // Diamant
            default => '#6B7280',
        };
    }

    /**
     * Upgrade to the next tier.
     */
    public function upgradeTier(): void
    {
        $maxTier = $this->achievement->tier ?? 3;
        
        if ($this->current_tier < $maxTier) {
            $this->update([
                'current_tier' => $this->current_tier + 1,
                'completed_at' => null,
                'claimed_at' => null,
                'progress' => [
                    'current' => 0,
                    'target' => 0,
                    'percentage' => 0,
                ],
            ]);
        }
    }
}