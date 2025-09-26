<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model {
    public $timestamps = false;
    protected $fillable = ['course_id','user_id','enrolled_at'];
    protected $dates = ['enrolled_at'];
    public function course(){ return $this->belongsTo(Course::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
