<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'question_type',
        'explanation',
        'points',
        'order',
    ];

    protected $casts = [
        'points' => 'integer',
        'order' => 'integer',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class)->orderBy('order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    public function getCorrectOptions()
    {
        return $this->options()->where('is_correct', true)->get();
    }

    public function getCorrectOptionIds(): array
    {
        return $this->options()
            ->where('is_correct', true)
            ->pluck('id')
            ->toArray();
    }

    public function checkAnswer($answer): bool
    {
        if ($this->question_type === 'single' || $this->question_type === 'true_false') {
            $correctOption = $this->options()->where('is_correct', true)->first();
            return $correctOption && $answer == $correctOption->id;
        }

        if ($this->question_type === 'multiple') {
            $correctOptions = $this->getCorrectOptionIds();
            $userAnswers = is_array($answer) ? $answer : [];
            
            sort($correctOptions);
            sort($userAnswers);
            
            return $correctOptions == $userAnswers;
        }

        return false;
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->question_type) {
            'single' => 'Choix unique',
            'multiple' => 'Choix multiple',
            'true_false' => 'Vrai/Faux',
            'text' => 'Réponse texte',
            default => 'Inconnu'
        };
    }
}