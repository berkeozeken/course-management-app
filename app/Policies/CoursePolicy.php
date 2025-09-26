<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(?User $user): bool { return true; }
    public function view(?User $user, Course $course): bool { return true; }

    public function create(User $user): bool {
        return in_array($user->role, ['instructor','admin']);
    }

    public function update(User $user, Course $course): bool {
        return $user->role === 'admin'
            || ($user->role === 'instructor' && $course->owner_id === $user->id);
    }

    public function delete(User $user, Course $course): bool {
        return $this->update($user, $course);
    }
}
