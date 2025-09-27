<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    // Migration'da created_at/updated_at var; timestamps açık kalsın.
    protected $fillable = ['user_id', 'course_id', 'status'];

    protected $casts = [
        'status' => 'string', // 'active' | 'cancelled'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
