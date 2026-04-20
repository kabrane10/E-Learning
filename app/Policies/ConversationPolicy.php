<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConversationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->isParticipant($user->id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Conversation $conversation): bool
    {
        $participant = $conversation->participants()
            ->where('user_id', $user->id)
            ->first();
            
        return $participant && $participant->role === 'admin';
    }

    public function delete(User $user, Conversation $conversation): bool
    {
        return $conversation->created_by === $user->id;
    }
}