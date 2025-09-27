<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $u){ return in_array($u->role, ['instructor','admin']); }
    public function view(?User $u, Course $c){ return true; }
    public function create(User $u){ return in_array($u->role, ['instructor','admin']); }
    public function update(User $u, Course $c){ return in_array($u->role, ['instructor','admin']); }
    public function delete(User $u, Course $c){ return in_array($u->role, ['instructor','admin']); }
}
