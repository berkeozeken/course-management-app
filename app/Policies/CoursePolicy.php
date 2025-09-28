<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function view(User $user, Course $course): bool
    {
        if (in_array($user->role, ['admin','instructor'])) {
            return true;
        }
        return (bool) $course->is_published; // student için sadece yayınlanmış
    }

    public function enroll(User $user, Course $course): bool
    {
        // Sadece student ve yalnızca yayınlanmış kurs
        if ($user->role !== 'student') {
            return false;
        }
        return (bool) $course->is_published;
    }

    // İsteğe bağlı diğer örnekler:
    public function update(User $user, Course $course): bool
    {
        return in_array($user->role, ['admin','instructor']) && $course->user_id === $user->id || $user->role === 'admin';
    }

    public function publish(User $user, Course $course): bool
    {
        return in_array($user->role, ['admin','instructor']);
    }
}
