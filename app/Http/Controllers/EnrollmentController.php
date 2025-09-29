<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function store(Course $course)
    {
        $userId = Auth::id();

        // güvenlik: published değilse ya da eğitmenin kendisiyse engelle
        if (!$course->is_published || $course->instructor_id === $userId) {
            return redirect()->route('courses.show', $course->id)
                ->with('error', 'Enrollment is not available for this course.');
        }

        $course->enroll($userId);

        // KURSTA KAL → sağdaki panelde "Unenroll" + "Start Learning" görünecek
        return redirect()->route('courses.show', $course->id)
            ->with('success', 'Enrolled successfully.');
    }

    public function destroy(Course $course)
    {
        $course->unenroll(Auth::id());

        // yine kursta kal
        return redirect()->route('courses.show', $course->id)
            ->with('success', 'Enrollment removed.');
    }
}
