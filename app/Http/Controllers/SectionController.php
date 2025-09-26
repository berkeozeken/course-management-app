<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function store(Request $request, Course $course)
    {
        // sadece owner (instructor) / admin
        $this->authorize('update', $course);

        $data = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $nextPos = (int) $course->sections()->max('position') + 1;

        Section::create([
            'course_id' => $course->id,
            'title'     => $data['title'],
            'position'  => $nextPos,
        ]);

        return redirect()->route('courses.show', $course->slug);
    }
}
