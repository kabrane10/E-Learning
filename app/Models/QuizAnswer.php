<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_attempt_id',
        'question_id',
        'answer_data',
        'is_correct',
        'points_earned',
    ];

    protected $casts = [
        'answer_data' => 'array',
        'is_correct' => 'boolean',
        'points_earned' => 'integer',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function getAnswerTextAttribute(): string
    {
        if (!$this->answer_data) {
            return 'Aucune réponse';
        }

        if (is_array($this->answer_data)) {
            // Récupérer le texte des options sélectionnées
            $options = QuizOption::whereIn('id', $this->answer_data)
                ->pluck('option_text')
                ->toArray();
            
            return implode(', ', $options);
        }

        // Pour les questions de type texte
        return (string) $this->answer_data;
    }
}