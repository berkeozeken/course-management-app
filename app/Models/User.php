<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // student | instructor | admin
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Helper: quick role checks
    public function isStudent(): bool { return $this->role === 'student'; }
    public function isInstructor(): bool { return $this->role === 'instructor'; }
    public function isAdmin(): bool { return $this->role === 'admin'; }

    // >>> My Courses için ilişki
    public function enrolledCourses()
    {
        return $this->belongsToMany(\App\Models\Course::class, 'course_user')->withTimestamps();
    }
}
