<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ForumCategory;

class ForumCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ForumCategory $category): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin']);
    }

    public function update(User $user, ForumCategory $category): bool
    {
        return $user->hasRole(['admin']);
    }

    public function delete(User $user, ForumCategory $category): bool
    {
        return $user->hasRole(['admin']);
    }
}