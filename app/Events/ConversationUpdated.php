<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Conversation $conversation,
        public string $action, // 'title_updated', 'participant_added', 'participant_removed', 'muted', 'pinned'
        public array $data = []
    ) {
        $this->conversation->load(['participants.user', 'lastMessage.user']);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [];
        
        // Diffuser à tous les participants de la conversation
        foreach ($this->conversation->participants as $participant) {
            $channels[] = new PrivateChannel('user.' . $participant->user_id);
        }
        
        // Également sur le canal de la conversation
        $channels[] = new PrivateChannel('conversation.' . $this->conversation->id);
        
        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'conversation.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'action' => $this->action,
            'data' => $this->data,
            'conversation' => [
                'id' => $this->conversation->id,
                'type' => $this->conversation->type,
                'title' => $this->conversation->title,
                'course_id' => $this->conversation->course_id,
                'last_message_at' => $this->conversation->last_message_at?->toISOString(),
                'participants_count' => $this->conversation->participants->count(),
                'participants' => $this->conversation->participants->map(fn($p) => [
                    'user_id' => $p->user_id,
                    'user_name' => $p->user->name,
                    'user_avatar' => $p->user->avatar,
                    'role' => $p->role,
                    'is_online' => $this->isUserOnline($p->user_id),
                ]),
                'last_message' => $this->conversation->lastMessage ? [
                    'id' => $this->conversation->lastMessage->id,
                    'content' => $this->conversation->lastMessage->content,
                    'type' => $this->conversation->lastMessage->type,
                    'user' => [
                        'id' => $this->conversation->lastMessage->user->id,
                        'name' => $this->conversation->lastMessage->user->name,
                    ],
                    'created_at' => $this->conversation->lastMessage->created_at->toISOString(),
                ] : null,
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Determine if a user is online.
     */
    private function isUserOnline(int $userId): bool
    {
        // Vérifier si l'utilisateur a une présence active
        // Peut être implémenté avec Laravel Reverb Presence Channels
        return cache()->has("user-online-{$userId}");
    }
}