<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level_number',
        'points_required',
        'icon',
        'color',
        'rewards',
    ];

    protected $casts = [
        'level_number' => 'integer',
        'points_required' => 'integer',
        'rewards' => 'array',
    ];

    /**
     * Get the level for a given points amount.
     */
    public static function getLevelForPoints(int $points): self
    {
        $level = self::where('points_required', '<=', $points)
            ->orderBy('level_number', 'desc')
            ->first();
            
        if (!$level) {
            $level = self::where('level_number', 1)->first();
        }
        
        return $level;
    }

    /**
     * Get the next level.
     */
    public static function getNextLevel(int $currentLevel): ?self
    {
        return self::where('level_number', '>', $currentLevel)
            ->orderBy('level_number', 'asc')
            ->first();
    }

    /**
     * Calculate progress to next level.
     */
    public function getProgressToNextLevel(int $points): float
    {
        $nextLevel = self::getNextLevel($this->level_number);
        
        if (!$nextLevel) {
            return 100;
        }

        $currentLevelPoints = $this->points_required;
        $nextLevelPoints = $nextLevel->points_required;
        
        if ($nextLevelPoints <= $currentLevelPoints) {
            return 100;
        }
        
        $progress = (($points - $currentLevelPoints) / ($nextLevelPoints - $currentLevelPoints)) * 100;
        
        return min(100, max(0, $progress));
    }

    /**
     * Scope ordered by level number.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('level_number', 'asc');
    }
}