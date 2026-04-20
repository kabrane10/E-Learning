<?php

namespace App\Notifications;

use App\Models\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class QuizPassed extends Notification
{
    use Queueable;

    public function __construct(
        public Quiz $quiz,
        public int $score
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'quiz_passed',
            'quiz_id' => $this->quiz->id,
            'quiz_title' => $this->quiz->title,
            'score' => $this->score,
            'message' => 'Vous avez réussi le quiz "' . $this->quiz->title . '" avec un score de ' . $this->score . '%.',
            'icon' => '✅',
            'action_url' => route('student.learn', $this->quiz->lesson->course),
        ];
    }
}