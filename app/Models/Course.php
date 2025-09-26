<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['title','slug','description','status','owner_id'];

    public function owner(){ return $this->belongsTo(User::class,'owner_id'); }
    public function sections(){ return $this->hasMany(Section::class)->orderBy('position'); }
    public function enrollments(){ return $this->hasMany(Enrollment::class); }
}
