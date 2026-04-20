<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ForumPost;
use App\Models\ForumTopic;

class ForumPostPolicy
{
    public function create(User $user, ForumTopic $topic): bool
    {
        return $topic->status !== 'closed';
    }

    public function update(User $user, ForumPost $post): bool
    {
        return $user->id === $post->user_id || $user->hasRole(['admin', 'instructor']);
    }

    public function delete(User $user, ForumPost $post): bool
    {
        return $user->id === $post->user_id || $user->hasRole('admin');
    }
}