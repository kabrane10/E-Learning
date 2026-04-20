<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ForumTopic;

class ForumTopicPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ForumTopic $topic): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ForumTopic $topic): bool
    {
        return $user->id === $topic->user_id || $user->hasRole(['admin', 'instructor']);
    }

    public function delete(User $user, ForumTopic $topic): bool
    {
        return $user->id === $topic->user_id || $user->hasRole('admin');
    }
}