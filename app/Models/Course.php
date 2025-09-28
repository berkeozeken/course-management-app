<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'cover_url',
        'is_published',
        'instructor_id',
        'start_date',     // eklendi
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'price'        => 'decimal:2',
        'start_date'   => 'date',      // eklendi
    ];

    /* =========================
     |  RELATIONS
     |=========================*/

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('position');
    }

    // Kurs -> Section -> Lesson (tüm dersler, sırayla)
    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Section::class)
            ->orderBy('lessons.position');
    }

    public function students()
    {
        // pivot tablon mevcut koda göre: course_user
        return $this->belongsToMany(User::class, 'course_user')->withTimestamps();
    }

    /* =========================
     |  SCOPES
     |=========================*/

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }

    // Giriş yapan rolüne göre görünür kurslar
    public function scopeVisibleFor($q, ?User $user = null)
    {
        $user = $user ?: Auth::user();
        $role = strtolower($user->role ?? 'guest');

        if ($role === 'student' || !$user) {
            return $q->where('is_published', true);
        }
        // admin/instructor hepsini görür
        return $q;
    }

    /* =========================
     |  HELPERS
     |=========================*/

    public function isEnrolledBy(?int $userId): bool
    {
        if (!$userId) return false;
        return $this->students()->whereKey($userId)->exists();
    }

    public function enroll(int $userId): void
    {
        $this->students()->syncWithoutDetaching([$userId]);
    }

    public function unenroll(int $userId): void
    {
        $this->students()->detach($userId);
    }

    public function isInstructor(?int $userId): bool
    {
        return $userId && $this->instructor_id === $userId;
    }

    // İlk dersin id'sini verir (önce load edilmiş section/lesson varsa ordan, yoksa sorgu)
    public function firstLessonId(): ?int
    {
        if ($this->relationLoaded('sections')) {
            foreach ($this->sections as $s) {
                if ($s->relationLoaded('lessons') && $s->lessons->count()) {
                    return $s->lessons->first()->id;
                }
            }
        }
        $first = $this->lessons()->first(['lessons.id']);
        return $first?->id;
    }
}
