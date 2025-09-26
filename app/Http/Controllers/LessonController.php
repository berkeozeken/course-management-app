<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function store(Request $request, Section $section)
    {
        // Sadece kurs sahibi / admin (CoursePolicy@update üzerinden)
        $this->authorize('update', $section->course);

        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'is_free'    => 'boolean',
            'video_url'  => 'nullable|url|max:2048',
        ]);

        $nextPos = (int) $section->lessons()->max('position') + 1;

        Lesson::create([
            'section_id' => $section->id,
            'title'      => $data['title'],
            'is_free'    => (bool) ($data['is_free'] ?? false),
            'video_url'  => $data['video_url'] ?? null,
            'position'   => $nextPos,
        ]);

        return back();
    }
}
