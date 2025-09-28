<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'section_id','title','content','video_url','duration_minutes','is_free','position'
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'duration_minutes' => 'integer',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
