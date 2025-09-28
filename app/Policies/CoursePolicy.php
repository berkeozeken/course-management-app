<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    // Admin her şeyi görür/yapar
    protected function isAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }

    // Instructor kendi kurslarını yönetir
    protected function owns(User $user, Course $course): bool
    {
        return (int)$course->instructor_id === (int)$user->id;
    }

    /**
     * Listeler (index).
     * - Admin: tümü
     * - Instructor: kendi kursları (UI'da filtre uygulayabilirsin)
     * - Student: yalnızca published olanları görmeli (controller’da scope/policy birlikte)
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'instructor', 'student'], true);
    }

    /**
     * Tek kurs görüntüleme.
     * - Admin: evet
     * - Owner instructor: evet
     * - Student: sadece published ise evet
     */
    public function view(User $user, Course $course): bool
    {
        if ($this->isAdmin($user) || $this->owns($user, $course)) return true;

        if ($user->role === 'student') {
            return (bool)$course->is_published;
        }

        return false;
    }

    /**
     * Kurs oluşturma.
     * - Admin & Instructor: evet
     * - Student: hayır
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'instructor'], true);
    }

    /**
     * Kurs güncelleme.
     * - Admin: evet
     * - Owner instructor: evet
     * - Student: hayır
     */
    public function update(User $user, Course $course): bool
    {
        return $this->isAdmin($user) || $this->owns($user, $course);
    }

    /**
     * Kurs silme.
     * - Admin: evet
     * - Owner instructor: evet
     * - Student: hayır
     */
    public function delete(User $user, Course $course): bool
    {
        return $this->isAdmin($user) || $this->owns($user, $course);
    }

    /**
     * Publish / Unpublish.
     * - Admin: evet
     * - Owner instructor: evet
     */
    public function publish(User $user, Course $course): bool
    {
        return $this->isAdmin($user) || $this->owns($user, $course);
    }
}
