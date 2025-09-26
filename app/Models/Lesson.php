<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = ['section_id','title','is_free','video_url','position'];

    public function section(){ return $this->belongsTo(Section::class); }
}
