<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function store(Course $course)
    {
        $user = Auth::user();
        if (!$user) abort(403);

        if (!$course->is_published) {
            return back()->with('error', 'Taslak kursa kayıt olunamaz.');
        }

        // ilişki adı student(s) veya enrollments'e göre uyumlu olsun
        if (method_exists($course, 'students')) {
            $course->students()->syncWithoutDetaching([$user->id]);
        } else {
            // eğer farklıysa, uygun ilişkiyi kullan
            $course->enrollments()->create(['user_id' => $user->id]);
        }

        return back()->with('success', 'Kursa kayıt oldunuz.');
    }

    public function destroy(Course $course)
    {
        $user = Auth::user();
        if (!$user) abort(403);

        if (method_exists($course, 'students')) {
            $course->students()->detach($user->id);
        } else {
            $course->enrollments()->where('user_id', $user->id)->delete();
        }

        return back()->with('success', 'Kayıt silindi.');
    }
}
