<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Tout le monde peut voir la liste
    }

    public function view(User $user, Course $course): bool
    {
        // Si le cours est publié OU si l'utilisateur est le formateur OU admin
        return $course->is_published || 
               $user->id === $course->instructor_id || 
               $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'instructor']);
    }

    public function update(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id || $user->hasRole('admin');
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id || $user->hasRole('admin');
    }

    public function manage(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id || $user->hasRole('admin');
    }
}