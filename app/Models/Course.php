<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['title','slug','description','status','owner_id'];

    public function owner(){ return $this->belongsTo(User::class,'owner_id'); }
    public function sections(){ return $this->hasMany(Section::class)->orderBy('position'); }
    public function enrollments(){ return $this->hasMany(\App\Models\Enrollment::class); }

    public function students(){return $this->belongsToMany(\App\Models\User::class, 'enrollments')    ->withTimestamps()    ->withPivot('status');    }
}
