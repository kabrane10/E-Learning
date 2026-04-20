<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'enrollment_id',
        'watched_duration',
    ];

    protected $casts = [
        'watched_duration' => 'integer',
    ];

    protected static function booted()
    {
        static::created(function (LessonCompletion $completion) {
            // Mettre à jour la progression de l'inscription
            if ($completion->enrollment) {
                $completion->enrollment->updateProgress();
            }
        });

        static::deleted(function (LessonCompletion $completion) {
            // Mettre à jour la progression de l'inscription
            if ($completion->enrollment) {
                $completion->enrollment->updateProgress();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}