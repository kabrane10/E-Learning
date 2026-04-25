<?php

namespace App\Notifications;

use App\Models\ForumPost;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Support\Str; 

class NewForumReply extends Notification
{
    use Queueable;

    public function __construct(public ForumPost $post)
    {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $topic = $this->post->topic;
        $replyAuthor = $this->post->user->name;
        
        return (new MailMessage)
            ->subject('📬 Nouvelle réponse : ' . $topic->title)
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line($replyAuthor . ' a répondu au sujet "' . $topic->title . '".')
            ->line('"' . Str::limit($this->post->content, 200) . '"')
            ->action('Voir la réponse', route('forum.topics.show', $topic) . '#post-' . $this->post->id)
            ->line('Merci de participer à la communauté !');
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toArray($notifiable): array
    {
        $topic = $this->post->topic;
        
        return [
            'type' => 'forum_reply',
            'topic_id' => $topic->id,
            'topic_title' => $topic->title,
            'post_id' => $this->post->id,
            'reply_author' => $this->post->user->name,
            'message' => $this->post->user->name . ' a répondu au sujet "' . $topic->title . '"',
            'icon' => '💬',
            'action_url' => route('forum.topics.show', $topic) . '#post-' . $this->post->id,
        ];
    }

    /**
     * Get the database representation of the notification.
     * (Alternative à toArray pour les notifications database)
     */
    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}