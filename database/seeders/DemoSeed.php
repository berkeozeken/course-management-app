<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\Enrollment;

class DemoSeed extends Seeder {
    public function run(): void {
        $instructor = User::factory()->create([
    'name'=>'Demo Instructor',
    'email'=>'instructor@example.com',
    'password'=>Hash::make('password'),
    'role'=>'instructor',
]);
        $student = User::factory()->create([
    'name'=>'Demo Student',
    'email'=>'student@example.com',
    'password'=>Hash::make('password'),
    'role'=>'student',
]);

        $course = Course::create([
            'title'=>'Laravel + Vue 101','slug'=>'laravel-vue-101',
            'description'=>'Giriş düzeyi kurs','status'=>'published','owner_id'=>$instructor->id,
        ]);

        $s1 = Section::create(['course_id'=>$course->id,'title'=>'Giriş','position'=>1]);
        $s2 = Section::create(['course_id'=>$course->id,'title'=>'Temeller','position'=>2]);

        Lesson::create(['section_id'=>$s1->id,'title'=>'Kurs Tanıtımı','position'=>1,'is_free'=>true]);
        Lesson::create(['section_id'=>$s2->id,'title'=>'Routing','position'=>1,'is_free'=>false]);

        Enrollment::create(['course_id'=>$course->id,'user_id'=>$student->id,'enrolled_at'=>now()]);
    }
}
