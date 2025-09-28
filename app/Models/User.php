<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /* =========================
     |  RELATIONS
     |=========================*/

    // Öğrenci olarak kayıtlı olduğu kurslar
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user')->withTimestamps();
    }

    // Eğitmen olduğu kurslar
    public function instructedCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /* =========================
     |  ROLE HELPERS
     |=========================*/

    public function role(): string
    {
        return strtolower($this->attributes['role'] ?? 'student');
    }

    public function isAdmin(): bool      { return $this->role() === 'admin'; }
    public function isInstructor(): bool { return $this->role() === 'instructor'; }
    public function isStudent(): bool    { return $this->role() === 'student'; }

    /* =========================
     |  HELPERS
     |=========================*/

    public function enrolledIn(int $courseId): bool
    {
        return $this->courses()->whereKey($courseId)->exists();
    }

    // Güvenli parola set etme (zaten hash'li ise tekrar hash'leme)
    public function setPasswordAttribute($value): void
    {
        if (!$value) return;
        $this->attributes['password'] = str_starts_with((string)$value, '$2y$')
            ? $value
            : Hash::make($value);
    }
}
