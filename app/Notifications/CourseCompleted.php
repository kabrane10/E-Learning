<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class CourseCompleted extends Notification
{
    use Queueable;

    public function __construct(public Course $course)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Félicitations ! Vous avez terminé le cours !')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Vous avez terminé avec succès le cours "' . $this->course->title . '".')
            ->line('Votre certificat est maintenant disponible.')
            ->action('Voir mon certificat', route('student.certificate', $this->course))
            ->line('Continuez votre apprentissage avec nos autres cours !');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'course_completed',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'message' => 'Félicitations ! Vous avez terminé le cours "' . $this->course->title . '".',
            'icon' => '🏆',
            'action_url' => route('student.certificate', $this->course),
        ];
    }
}