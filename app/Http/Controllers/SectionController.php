<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SectionController extends Controller
{
    private function ensureInstructor(): void
    {
        $role = strtolower(Auth::user()->role ?? '');
        abort_unless(in_array($role, ['admin','instructor'], true), 403);
    }

    public function create(Course $course)
    {
        $this->ensureInstructor();
        return Inertia::render('Sections/Create', [
            'course' => $course->only(['id','title']),
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $this->ensureInstructor();

        $data = $request->validate([
            'title'    => ['required','string','max:255'],
            'position' => ['nullable','integer','min:1'],
        ]);

        $position = $data['position'] ?? (int)($course->sections()->max('position') + 1);

        Section::create([
            'course_id' => $course->id,
            'title'     => $data['title'],
            'position'  => $position,
        ]);

        return redirect()->route('courses.show', $course->id)->with('success','Bölüm oluşturuldu.');
    }

    public function edit(Section $section)
    {
        $this->ensureInstructor();
        $section->load('course:id,title');
        return Inertia::render('Sections/Edit', ['section' => $section]);
    }

    public function update(Request $request, Section $section)
    {
        $this->ensureInstructor();

        $data = $request->validate([
            'title'    => ['required','string','max:255'],
            'position' => ['nullable','integer','min:1'],
        ]);

        $section->update($data);

        return redirect()->route('courses.show', $section->course_id)->with('success','Bölüm güncellendi.');
    }

    public function destroy(Section $section)
    {
        $this->ensureInstructor();
        $courseId = $section->course_id;
        $section->delete();

        return redirect()->route('courses.show', $courseId)->with('success','Bölüm silindi.');
    }
}
