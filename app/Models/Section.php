<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['course_id','title','position'];

    public function course()  { return $this->belongsTo(\App\Models\Course::class); }
    public function lessons() { return $this->hasMany(\App\Models\Lesson::class); }
}
